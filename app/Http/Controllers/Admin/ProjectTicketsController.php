<?php

namespace App\Http\Controllers\Admin;

use App\ClientProject;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientProjectsTicketRequest;
use App\Http\Requests\Admin\UpdateClientProjectsTicketRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Kordy\Ticketit\Models\Ticket;
/*use App\ProjectStatus;*/
use Kordy\Ticketit\Models;
use Kordy\Ticketit\Models\Agent;
use Kordy\Ticketit\Models\Category;
use Cache;
use Kordy\Ticketit\Helpers\LaravelVersion;
use Kordy\Ticketit\Models\Setting;
class ProjectTicketsController extends Controller
{
    
    /**
     * Display a listing of MileStone.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $project_id = '' )
    {
        if (! Gate::allows('project_ticket_access')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        

        if (request()->ajax()) {
            $query = Ticket::query();
            $query->where('project_id', '=', $project_id);
            $template = 'actionsTemplate';
            
            $query
            ->join('contacts', 'contacts.id', '=', 'ticketit.user_id')
            ->join('ticketit_statuses', 'ticketit_statuses.id', '=', 'ticketit.status_id')
            ->join('ticketit_priorities', 'ticketit_priorities.id', '=', 'ticketit.priority_id')
            ->join('ticketit_categories', 'ticketit_categories.id', '=', 'ticketit.category_id')
            ->select([
                'ticketit.id',
                'ticketit.created_at',
                'ticketit.updated_at',
                'ticketit.subject AS subject',
                'ticketit_statuses.name AS status',
                'ticketit_statuses.color AS color_status',
                'ticketit_priorities.color AS color_priority',
                'ticketit_categories.color AS color_category',
                
                'ticketit.updated_at AS updated_at',
                'ticketit_priorities.name AS priority',
                'contacts.name AS owner',
                'ticketit.agent_id',
                'ticketit_categories.name AS category',
                'ticketit.project_id',
                
            ]);

            /// Custom Filters Start.  
            $query->when(request('date_filter', false), function ($q, $date_filter) {
                $parts = explode(' - ' , $date_filter);
                $date_type = request('date_type');
                $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
                $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');
                if ( ! empty( $date_type ) ) {
                    if ( in_array($date_type, array( 'created_at') ) ) {
                        $column = DB::getTablePrefix() . 'ticketit.created_at';
                        return $q->where(DB::raw('date('.$column.')'), '>=', $date_from)->where(DB::raw('date('.$column.')'), '<=', $date_to);
                    } else {
                        return $q->whereBetween('ticketit.'.$date_type, [$date_from, $date_to]);
                    }
                }
            });
            $query->when(request('priority', false), function ($q, $priority) { 
                return $q->where('priority_id', $priority);
            });
            $query->when(request('status', false), function ($q, $ticket_status) { 
                return $q->where('ticketit.status_id', $ticket_status);
            });

          
            /// Custom Filters End.
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->editColumn('updated_at', function ($row) {
                return $row->updated_at ? digiDate( $row->updated_at, true ) : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? digiDate( $row->created_at, true) : '';
            });
            $table->editColumn('agent', function ($row) {
                return $row->agent ? $row->agent->name : '';
            });

            

            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_ticket_';
                $routeKey = 'admin.project_tickets';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.project_tickets.index', array('client_project' => $project));
    }

    
    /**
     * Show the form for creating new Ticket.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = '' )
    {
        if (! Gate::allows('project_ticket_create')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        list($priorities, $categories ,$status_lists) = $this->PCS();
            
        return view('admin.project_tickets.create', array( 'client_project' => $project, 'priorities' => $priorities, 'categories' => $categories,'status_lists' => $status_lists));
    }

    /**
     * Returns priorities, categories and statuses lists in this order
     * Decouple it with list().
     *
     * @return array
     */
    protected function PCS()
    {
  
        $priorities = Models\Priority::all();
 
        $categories = Models\Category::all();

        $statuses = Models\Status::all();
 return [$priorities->lists('name', 'id')->prepend(trans('global.app_please_select'), ''), $categories->lists('name', 'id')->prepend(trans('global.app_please_select'), ''), $statuses->lists('name', 'id')->prepend(trans('global.app_please_select'), '')];
 
        // if (LaravelVersion::min('5.3.0')) {
        //     return [$priorities->pluck('name', 'id')->prepend(trans('global.app_please_select'), ''), $categories->pluck('name', 'id')->prepend(trans('global.app_please_select'), ''), $statuses->pluck('name', 'id')->prepend(trans('global.app_please_select'), '')];
        // } else {
        //     return [$priorities->lists('name', 'id')->prepend(trans('global.app_please_select'), ''), $categories->lists('name', 'id')->prepend(trans('global.app_please_select'), ''), $statuses->lists('name', 'id')->prepend(trans('global.app_please_select'), '')];
        // }
    }

    /**
     * Store a newly created Ticket in storage.
     *
     * @param  \App\Http\Requests\StoreMileStonesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientProjectsTicketRequest $request, $project_id = '')
    {
        if (! Gate::allows('project_ticket_create')) {
            return abort(401);
        }
        
        $ticket = new Ticket();

        $ticket->subject = $request->subject;

        $ticket->content = $request->content;

        $ticket->setPurifiedContent($request->get('content'));

        $ticket->priority_id = $request->priority_id;
        $ticket->category_id = $request->category_id;

        $ticket->status_id = $request->status_id;
        $ticket->user_id = auth()->user()->id;
        $ticket->project_id = $project_id;
        $ticket->autoSelectAgent();

        $ticket->save();

        flashMessage( 'success', 'create');

        return redirect()->route('admin.project_tickets.index', $project_id);
    }


    /**
     * Show the form for editing Ticket.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        if (! Gate::allows('project_ticket_edit')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $ticket = Ticket::findOrFail($id);
        
        list($priorities, $categories, $status_lists) = $this->PCS();

        $cat_agents = Models\Category::find($ticket->category_id)->agents()->agentsLists();
        if (is_array($cat_agents)) {
            $agent_lists = ['auto' => 'Auto Select'] + $cat_agents;
        } else {
            $agent_lists = ['auto' => 'Auto Select'];
        }

        $comments = $ticket->comments()->paginate(Setting::grab('paginate_items'));
            
        

        return view('admin.project_tickets.edit', array( 'ticket' => $ticket, 'client_project' => $project, 'priorities' => $priorities, 'categories' => $categories, 'status_lists' => $status_lists, 'agent_lists' => $agent_lists, 'comments' => $comments));
    }

    /**
     * Update Ticket in storage.
     *
     * @param  \App\Http\Requests\UpdateMileStonesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientProjectsTicketRequest $request, $project_id, $id)
    {
        if (! Gate::allows('project_ticket_edit')) {
            return abort(401);
        }
        $ticket = Ticket::findOrFail($id);

        $ticket->subject = $request->subject;

        $ticket->setPurifiedContent($request->get('content'));

        $ticket->status_id = $request->status_id;
        $ticket->category_id = $request->category_id;
        $ticket->priority_id = $request->priority_id;

        if ($request->input('agent_id') == 'auto') {
            $ticket->autoSelectAgent();
        } else {
            $ticket->agent_id = $request->input('agent_id');
        }

        if ( $request->status_id == PROJECT_TASK_STATUS_COMPLETED ) {
            $ticket->completed_at = \Carbon\Carbon::now();
        } else {
            $ticket->completed_at = null;
        }

        $ticket->save();

        flashMessage( 'success', 'update');

        return redirect()->route('admin.project_tickets.index', $project_id);
    }


    /**
     * Display MileStone.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $ticket_id)
    {
        if (! Gate::allows('project_ticket_view')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $ticket = Ticket::findOrFail($ticket_id);

        return view('admin.project_tickets.show', compact('ticket', 'project'));
    }


    /**
     * Remove Ticket from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $project_id, $id)
    {
        if (! Gate::allows('project_ticket_delete')) {
            return abort(401);
        }
        $mile_stone = Ticket::findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->delete();

        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.expense_categories.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Ticket at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('project_ticket_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Ticket::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Ticket from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($project_id, $id)
    {
        if (! Gate::allows('project_ticket_delete')) {
            return abort(401);
        }
        $mile_stone = Ticket::onlyTrashed()->findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete Ticket from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($project_id, $id)
    {
        if (! Gate::allows('project_ticket_delete')) {
            return abort(401);
        }
        $mile_stone = Ticket::onlyTrashed()->findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }


}