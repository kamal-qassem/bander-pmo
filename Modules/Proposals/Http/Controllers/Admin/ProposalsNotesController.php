<?php
namespace Modules\Proposals\Http\Controllers\Admin;

use Modules\Proposals\Entities\Proposal;
use Modules\Proposals\Entities\ProposalsNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Proposals\Http\Requests\Admin\StoreProposalsNotesRequest;
use Modules\Proposals\Http\Requests\Admin\UpdateProposalsNotesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class ProposalsNotesController extends Controller
{
    /**
     * Display a listing of ProposalsNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $proposal_id )
    {
       
        
        if (Gate::allows('proposal_note_access')) {
            return prepareBlockUserMessage();
        }
    
        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

  
        if (request()->ajax()) {
            $query = ProposalsNote::query();
            $query->with("proposal");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        
            abort_if (! Gate::allows('proposals_note_delete'), 401);


                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'proposals_notes.id',
                'proposals_notes.description',
                'proposals_notes.date_contacted',
                'proposals_notes.proposal_id',
                'proposals_notes.created_by_id',
            ]);
            $query->where('proposal_id', '=', $proposal_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'proposals_note_';
                $routeKey = 'admin.proposals_notes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';
                if ( ! empty( $description ) ) {
                    $description = Str::limit($description, 40, '<a href="'.route('admin.proposals_notes.show', ['proposal_id' => $row->proposal_id, 'id' => $row->id]).'">...</a>');
                }
                return $description;
            });

            
            $table->editColumn('date_contacted', function ($row) {
                return $row->date_contacted ? digiDate( $row->date_contacted ) : '';
            });
            $table->editColumn('proposal.status', function ($row) {
                return $row->proposal ? $row->proposal->status : '';
            });

               $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete', 'description']);

            return $table->make(true);
        }

        return view('proposals::admin.proposals.proposals_notes.index', compact('proposal'));
    }

    /**
     * Show the form for creating new ProposalsNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $proposal_id )
    {
        
        if (Gate::allows('proposal_note_create')) {
            return prepareBlockUserMessage();
        }

        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $proposals = Proposal::get()->pluck('status', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('proposals::admin.proposals.proposals_notes.create', compact('proposals','created_bies','proposal'));
    }

    /**
     * Store a newly created ProposalsNote in storage.
     *
     * @param  \App\Http\Requests\StoreProposalsNotesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProposalsNotesRequest $request, $proposal_id)
    {
        
        if ( Gate::allows('proposal_note_create')) {
            return prepareBlockUserMessage();
        }
        $addtional = array(
            'proposal_id' => $proposal_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposals_note = ProposalsNote::create($request->all());

        flashMessage( 'success', 'create');

        return redirect()->route('admin.proposals_notes.index', $proposal_id);
    }


    /**
     * Show the form for editing ProposalsNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($proposal_id, $id)
    {
        
        if (Gate::allows('proposal_note_edit')) {
            return prepareBlockUserMessage();
        }

        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $proposals = Proposal::get()->pluck('status', 'id')->prepend(trans('global.app_please_select'), '');

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $proposals_note = ProposalsNote::findOrFail($id);

        return view('proposals::admin.proposals.proposals_notes.edit', compact('proposals_note', 'created_bies','proposals', 'proposal'));
    }

    /**
     * Update ProposalsNote in storage.
     *
     * @param  \App\Http\Requests\UpdateProposalsNotesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProposalsNotesRequest $request, $proposal_id, $id)
    {
        if (Gate::allows('proposal_note_edit')) {
            return prepareBlockUserMessage();
        }

        $proposals_note = ProposalsNote::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposals_note->update($request->all());

        flashMessage( 'success', 'update');

        return redirect()->route('admin.proposals_notes.index', $proposal_id);
    }


    /**
     * Display ProposalsNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($proposal_id, $id)
    {

        if ( Gate::allows('proposal_note_view')) {
            return prepareBlockUserMessage();
        }

        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $proposals_note = ProposalsNote::findOrFail($id);

        return view('proposals::admin.proposals.proposals_notes.show', compact('proposals_note', 'proposal'));
    }


    /**
     * Remove ProposalsNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($proposal_id, $id)
    {
        if ( Gate::allows('proposal_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposals_note = ProposalsNote::findOrFail($id);
        $proposals_note->delete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.proposals_notes.index', $proposal_id);
    }

    /**
     * Delete all selected ProposalsNote at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (Gate::allows('proposal_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProposalsNote::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore ProposalsNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if ( Gate::allows('proposal_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposals_note = ProposalsNote::onlyTrashed()->findOrFail($id);
        $proposals_note->restore();

        flashMessage( 'success', 'restore');

        return redirect()->route('admin.proposals_notes.index', $proposals_note->proposal_id);
    }

    /**
     * Permanently delete ProposalsNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if ( Gate::allows('proposal_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposals_note = ProposalsNote::onlyTrashed()->findOrFail($id);

        $proposal_id = $proposals_note->proposal_id;
        
        $proposals_note->forceDelete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.proposals_notes.index', $proposal_id);
    }
}
