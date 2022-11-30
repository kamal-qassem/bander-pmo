<?php

namespace Modules\DatabaseBackup\Http\Controllers\Admin;

use Modules\DatabaseBackup\Entities\DatabaseBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\DatabaseBackup\Http\Requests\Admin\StoreDatabaseBackupsRequest;
use Modules\DatabaseBackup\Http\Requests\Admin\UpdateDatabaseBackupsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Artisan;
use Log;
use Storage;
use Spatie\Backup\BackupDestination\BackupDestination;
use Theme;

class DatabaseBackupsController extends Controller
{   

    public function __construct() {
        $this->middleware('plugin:databasebackup');
    }

    /**
     * Display a listing of DatabaseBackup.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = 'both' )
    {
        if (! Gate::allows('database_backup_access')) {
            return prepareBlockUserMessage();
        }

                
        if (request()->ajax()) {

            $type = request('type');
            
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

	        $backupname = preg_replace('/[^a-zA-Z0-9.]/', '-', config('backup.backup.name') );
	        if ( 'database' === $type ) {
	        	$backupname .= '-database';
	        } elseif ( 'files' === $type ) {
	        	$backupname .= '-files';
	        }

	        $files = $disk->files( $backupname );

	        $backups = [];
	        // make an array of backup files, with their filesize and creation date
	        foreach ($files as $k => $f) {
	            // only take the zip files into account
	            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
	                $backups[] = [
	                    'id' => $k,
	                    'file_path' => $f,
	                    'file_name' => str_replace($backupname . '/', '', $f),
	                    'file_size' => humanFilesize( $disk->size($f) ), // size of the file in bytes
	                    'last_modified' => $disk->lastModified($f),
	                ];
	            }
	        }
	        // reverse the backups, so the newest one would be on top
	        $backups = array_reverse($backups);

            $table = DataTables::of( $backups );

            $template = 'actionsTemplate';
            
            $table->setRowAttr([
                'data-entry-id' => function($row) {
                    $row = (Object)$row;
                    return $row->file_name;
                },
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template, $type) {
                $gateKey  = 'database_backup_';
                $routeKey = 'admin.database_backups';
                $row = (Object)$row;

                return view($template, compact('row', 'gateKey', 'routeKey', 'type'));
            });

            $table->editColumn('file_name', function ($row) {
                $row = (Object)$row;
                return $row->file_name ? '<a class="btn btn-xs btn-default"
                                   href="'. url('admin/backup/download/'.$row->file_name) .'"><i
                                        class="fa fa-cloud-download"></i>'.$row->file_name.'</a>' : '';
            });

            $table->editColumn('last_modified', function ($row) {
                $row = (Object)$row;
                return $row->last_modified ? digiDateTimestamp( $row->last_modified, true ) : '';
            });

            $table->rawColumns(['file_name', 'actions','massDelete']);

            return $table->make(true);
        }


        return view('databasebackup::admin.database_backups.index', compact('type'));
    }

    /**
     * Show the form for creating new DatabaseBackup.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('database_backup_create')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        return view('databasebackup::admin.database_backups.create');
    }

    /**
     * Store a newly created DatabaseBackup in storage.
     *
     * @param  \App\Http\Requests\StoreDatabaseBackupsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('database_backup_create')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $type = $request->name;

    	if ( 'database' === $type ) {
        	$backupconfig['backup.backup.name'] = config('app.name') . '-database';
        	config( $backupconfig ); // Write the dynamic values from DB.
        	\Artisan::call("backup:run", ['--only-db' => 'yes']);
    	} elseif ( 'files' === $type ) {
        	$backupconfig['backup.backup.name'] = config('app.name') . '-files';
        	config( $backupconfig ); // Write the dynamic values from DB.
        	\Artisan::call("backup:run", ['--only-files' => 'yes']);
    	} else {
        	\Artisan::call("backup:run");
    	}

        flashMessage('success', 'create');
        return redirect()->route('admin.database_backups.index');
    }


    /**
     * Show the form for editing DatabaseBackup.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('database_backup_edit')) {
            return prepareBlockUserMessage();
        }
        $database_backup = DatabaseBackup::findOrFail($id);

        return view('databasebackup::admin.database_backups.edit', compact('database_backup'));
    }

    /**
     * Update DatabaseBackup in storage.
     *
     * @param  \App\Http\Requests\UpdateDatabaseBackupsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDatabaseBackupsRequest $request, $id)
    {
        if (! Gate::allows('database_backup_edit')) {
            return prepareBlockUserMessage();
        }
        $database_backup = DatabaseBackup::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $database_backup->update($request->all());

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.database_backups.index');
    }


    /**
     * Display DatabaseBackup.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('database_backup_view')) {
            return prepareBlockUserMessage();
        }
        $database_backup = DatabaseBackup::findOrFail($id);

        return view('databasebackup::admin.database_backups.show', compact('database_backup'));
    }


    /**
     * Remove DatabaseBackup from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id, $type)
    {
        if (! Gate::allows('database_backup_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
       
        $this->delete( $id, $type );

        flashMessage( 'success', 'delete');

        if ( 'database' === $type ) {
        	return redirect()->route('admin.databasebackups.index', 'database');
        }
        if ( 'files' === $type ) {
        	return redirect()->route('admin.databasebackups.index', 'files');
        }

        return redirect()->route('admin.databasebackups.index', 'both');
    }

    /**
     * Delete all selected DatabaseBackup at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        
        if (! Gate::allows('database_backup_delete')) {
            return prepareBlockUserMessage();
        }

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        

        if ($request->input('ids')) {
            $entries = DatabaseBackup::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore DatabaseBackup from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('database_backup_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $database_backup = DatabaseBackup::onlyTrashed()->findOrFail($id);
        $database_backup->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete DatabaseBackup from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('database_backup_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $database_backup = DatabaseBackup::onlyTrashed()->findOrFail($id);
        $database_backup->forceDelete();

        return back();
    }

    /**
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
    public function download($file_name)
    {
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $backupname = preg_replace('/[^a-zA-Z0-9.]/', '-', config('backup.backup.name') );

        $file = $backupname . '/' . $file_name;
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        if ($disk->exists($file)) {
            $fs = Storage::disk(config('backup.backup.destination.disks')[0])->getDriver();
            $stream = $fs->readStream($file);
            return \Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $fs->getMimetype($file),
                "Content-Length" => $fs->getSize($file),
                "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
            ]);
        } else {
            abort(404, "The backup file doesn't exist.");
        }
    }

    /**
     * Deletes a backup file.
     */
    public function delete($file_name, $type = 'both')
    {
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $backupname = preg_replace('/[^a-zA-Z0-9.]/', '-', config('backup.backup.name') );
        if ( 'database' === $type ) {
        	$backupname .= '-database';
        } elseif ( 'files' === $type ) {
        	$backupname .= '-files';
        }

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        if ($disk->exists($backupname . '/' . $file_name)) {
            $disk->delete($backupname . '/' . $file_name);
            return true;
        } else {
            abort(404, "The backup file doesn't exist.");
        }
    }
}
