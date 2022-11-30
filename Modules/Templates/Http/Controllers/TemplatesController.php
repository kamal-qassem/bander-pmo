<?php

namespace Modules\Templates\Http\Controllers;



use Modules\Templates\Entities\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Templates\Http\Requests\Admin\StoreTemplatesRequest;
use Modules\Templates\Http\Requests\Admin\UpdateTemplatesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (! Gate::allows('template_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Template::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('template_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'templates.id',
                'templates.key',
                'templates.type',
                'templates.subject',
                'templates.from_email',
                'templates.from_name',
                'templates.content',
            ]);
			
			$query->orderBy('id', 'desc');
			
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'template_';
                $routeKey = 'admin.templates';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('from_email', function ($row) {
                return $row->from_email ? $row->from_email : '';
            });
            $table->editColumn('from_name', function ($row) {
                return $row->from_name ? $row->from_name : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('templates::admin.templates.index');
    }

    /**
     * Show the form for creating new Template.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('template_create')) {
            return prepareBlockUserMessage();
        }
         $enum_type = Template::$enum_type;
                   
        return view('templates::admin.templates.create', compact('enum_type'));
    }

    /**
     * Store a newly created Template in storage.
     *
     * @param  \App\Http\Requests\StoreTemplatesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTemplatesRequest $request)
    {
        if (! Gate::allows('template_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $template = Template::create($request->all());

        flashMessage( 'success', 'create' );

        return redirect()->route('admin.templates.index');
    }


    /**
     * Show the form for editing Template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('template_edit')) {
            return prepareBlockUserMessage();
        }  
                
              $enum_type = Template::$enum_type;
            
        $template = Template::findOrFail($id);

        return view('templates::admin.templates.edit', compact('template', 'enum_type'));
    }

    /**
     * Update Template in storage.
     *
     * @param  \App\Http\Requests\UpdateTemplatesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTemplatesRequest $request, $id)
    {
        if (! Gate::allows('template_edit')) {
            return prepareBlockUserMessage();
        }
        $template = Template::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $template->update($request->all());

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.templates.index');
    }


    /**
     * Display Template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('template_view')) {
            return prepareBlockUserMessage();
        }
        $template = Template::findOrFail($id);

        return view('templates::admin.templates.show', compact('template'));
    }


    /**
     * Remove Template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('template_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $template = Template::findOrFail($id);
        $template->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.templates.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Template at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('template_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Template::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('template_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $template = Template::onlyTrashed()->findOrFail($id);
        $template->restore();

        flashMessage( 'success', 'restore' );

        return back();
    }

    /**
     * Permanently delete Template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('template_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $template = Template::onlyTrashed()->findOrFail($id);
        $template->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }

    public function duplicate( $id ) {
        if (! Gate::allows('template_edit')) {
            return prepareBlockUserMessage();
        }

        $template = Template::find( $id );

        if (! $template) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $newtemplate = $template->replicate();

        $newtemplate->key = $template->key . '(duplicate)';

        $newtemplate->save();

        flashMessage( 'success', 'create', trans('others.duplicated'));
        return redirect()->route('admin.templates.index');
    }
}
