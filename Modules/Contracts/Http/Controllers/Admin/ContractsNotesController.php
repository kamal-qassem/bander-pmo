<?php
namespace Modules\Contracts\Http\Controllers\Admin;

use Modules\Contracts\Entities\Contract;
use Modules\Contracts\Entities\ContractsNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Contracts\Http\Requests\Admin\StoreContractsNotesRequest;
use Modules\Contracts\Http\Requests\Admin\UpdateContractsNotesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class ContractsNotesController extends Controller
{
    /**
     * Display a listing of ContractsNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $contract_id )
    {
        
        if (Gate::allows('contract_note_access')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
    
        if (request()->ajax()) {
            $query = ContractsNote::query();
            $query->with("contract");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        
            abort_if (! Gate::allows('contracts_note_delete'), 401);


                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contracts_notes.id',
                'contracts_notes.description',
                'contracts_notes.date_contacted',
                'contracts_notes.contract_id',
                'contracts_notes.created_by_id',
            ]);
            $query->where('contract_id', '=', $contract_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contracts_note_';
                $routeKey = 'admin.contracts_notes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';
                if ( ! empty( $description ) ) {
                    $description = Str::limit($description, 40, '<a href="'.route('admin.contracts_notes.show', ['contract_id' => $row->contract_id, 'id' => $row->id]).'">...</a>');
                }
                return $description;
            });

            
            $table->editColumn('date_contacted', function ($row) {
                return $row->date_contacted ? digiDate( $row->date_contacted ) : '';
            });
            $table->editColumn('contract.status', function ($row) {
                return $row->contract ? $row->contract->status : '';
            });

               $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete', 'description']);

            return $table->make(true);
        }

        return view('contracts::admin.contracts.contracts_notes.index', compact('contract'));
    }

    /**
     * Show the form for creating new ContractsNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $contract_id )
    {
        
        if ( Gate::allows('contract_note_create')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $contracts = Contract::get()->pluck('status', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('contracts::admin.contracts.contracts_notes.create', compact('contracts','created_bies','contract'));
    }

    /**
     * Store a newly created ContractsNote in storage.
     *
     * @param  \App\Http\Requests\StoreContractsNotesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractsNotesRequest $request, $contract_id)
    {
        
        if ( Gate::allows('contract_note_create')) {
            return prepareBlockUserMessage();
        }
        $addtional = array(
            'contract_id' => $contract_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contracts_note = ContractsNote::create($request->all());

        flashMessage( 'success', 'create');

        return redirect()->route('admin.contracts_notes.index', $contract_id);
    }


    /**
     * Show the form for editing ContractsNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($contract_id, $id)
    {
        
        if ( Gate::allows('contract_note_edit')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $contracts = Contract::get()->pluck('status', 'id')->prepend(trans('global.app_please_select'), '');

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $contracts_note = ContractsNote::findOrFail($id);

        return view('contracts::admin.contracts.contracts_notes.edit', compact('contracts_note', 'created_bies','contracts', 'contract'));
    }

    /**
     * Update ContractsNote in storage.
     *
     * @param  \App\Http\Requests\UpdateContractsNotesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractsNotesRequest $request, $contract_id, $id)
    {
        if ( Gate::allows('contract_note_edit')) {
            return prepareBlockUserMessage();
        }

        $contracts_note = ContractsNote::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contracts_note->update($request->all());

        flashMessage( 'success', 'update');

        return redirect()->route('admin.contracts_notes.index', $contract_id);
    }


    /**
     * Display ContractsNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($contract_id, $id)
    {

        if ( Gate::allows('contract_note_view')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $contracts_note = ContractsNote::findOrFail($id);

        return view('contracts::admin.contracts.contracts_notes.show', compact('contracts_note', 'contract'));
    }


    /**
     * Remove ContractsNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($contract_id, $id)
    {
        if ( Gate::allows('contract_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contracts_note = ContractsNote::findOrFail($id);
        $contracts_note->delete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.contracts_notes.index', $contract_id);
    }

    /**
     * Delete all selected ContractsNote at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if ( Gate::allows('contract_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContractsNote::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore ContractsNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if ( Gate::allows('contract_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contracts_note = ContractsNote::onlyTrashed()->findOrFail($id);
        $contracts_note->restore();

        flashMessage( 'success', 'restore');

        return redirect()->route('admin.contracts_notes.index', $contracts_note->contract_id);
    }

    /**
     * Permanently delete ContractsNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if ( Gate::allows('contract_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contracts_note = ContractsNote::onlyTrashed()->findOrFail($id);

        $contract_id = $contracts_note->contract_id;
        
        $contracts_note->forceDelete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.contracts_notes.index', $contract_id);
    }
}
