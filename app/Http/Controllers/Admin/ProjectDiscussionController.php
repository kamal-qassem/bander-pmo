<?php

namespace App\Http\Controllers\Admin;

use App\ClientProject;
use App\ProjectDiscussionComment;
use App\ProjectDiscussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectDiscussionsRequest;
use App\Http\Requests\Admin\UpdateProjectDiscussionsRequest;
use App\Http\Requests\Admin\StoreProjectDiscussionCommentsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FileUploadTrait;
class ProjectDiscussionController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ProjectDiscussion.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $project_id = '' )
    {
        if (! Gate::allows('project_discussion_access')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        if (request()->ajax()) {
            $query = ProjectDiscussion::query();
            $query->with("project");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('project_discussion_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'project_discussions.id',
                'project_discussions.subject',
                'project_discussions.description',
                'project_discussions.show_to_customer',
                'project_discussions.last_activity',
                'project_discussions.project_id',
                'project_discussions.created_by',
                'project_discussions.contact_id',
            ]);
            $query->where('project_id', '=', $project_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_discussion_';
                $routeKey = 'admin.project_discussions';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('subject', function ($row) {
                $name =  $row->subject ? '<a href="'.route('admin.project_discussions.comments', ['project_id' => $row->project_id, 'project_discussion_id' => $row->id]).'">' . $row->subject . '</a>' : '';
                if ( ! empty( $row->color ) ) {
                    $name = '<span style="color:'.$row->color.'">'.$name.'</span>';
                }
                return $name;
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('show_to_customer', function ($row) {
                return $row->show_to_customer ? ucfirst( $row->show_to_customer ) : '';
            });
            $table->editColumn('last_activity', function ($row) {
                return $row->last_activity ? digiDate( $row->last_activity, true ) : '';
            });
            $table->editColumn('project.title', function ($row) {
                return $row->project ? $row->project->title : '';
            });
            $table->editColumn('created_name.name', function ($row) {
                return $row->created_name ? $row->created_name->name : '';
            });
            $table->editColumn('contact.name', function ($row) {
                $name = $row->contact ? $row->contact->first_name : '';
                if ( ! empty( $name ) ) {
                    $name .= ' ' . $row->contact->last_name;
                }
                return $name;
            });
            $table->editColumn('total_comments', function ($row) {
                $comments = \App\ProjectDiscussionComment::where('project_id', $row->project_id)->where( 'discussion_id', $row->id)->count();
                return $comments;
            });

            $table->rawColumns(['actions','massDelete', 'subject']);

            return $table->make(true);
        }

        return view('admin.project_discussions.index', compact('project'));
    }

    public function projectDiscussionComments( $project_id = '', $project_discussion_id = '', $operation = '', $id = '' )

      {
        if (! Gate::allows('project_discussion_access')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        if (request()->ajax()) {
            $query = ProjectDiscussionComment::query();
            $query->with("project");
            $query->with("created_name");
            $query->with("contact");
            $query->with("parent_comment");            
            $template = 'actionsTemplate';
            
            $query->select([
                'project_discussion_comments.id',
                'project_discussion_comments.description',
                'project_discussion_comments.created_by_id',
                'project_discussion_comments.project_id',
                'project_discussion_comments.discussion_id',
                'project_discussion_comments.parent_id',
                'project_discussion_comments.attachment',
            ]);
            $query->where('project_discussion_comments.project_id', '=', $project_id );
            if ( ! empty( $project_discussion_id ) ) {
                $query->where('project_discussion_comments.discussion_id', '=', $project_discussion_id );
            }
            $query->leftJoin('project_discussion_comments as child', 'child.parent_id', '=', 'project_discussion_comments.id');
            
            $query->orderBy('project_discussion_comments.id');
            $query->orderBy('child.parent_id');
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->setRowClass(function ($row) {
                $class = 'parent';

                if ( $row->parent_id > 0 ) {
                    $class = 'child';
                }
                return $class;
            });
            
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_discussion_';
                $routeKey = 'admin.project_comment';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            
            $table->editColumn('name', function ($row) {
                $name = $row->created_name->name;
                $img = '';
                $details = $row->created_name;
                if ( $details && $details->contact_reference_id ) {
                    $contact = \App\Contact::find( $details->contact_reference_id );
                    if ( $contact ) {
                        $img = '<img src="'.asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail).'">&nbsp;';
                    }
                }
                $description = $row->description ? $row->description : '';
                $str = $row->created_name ? '<a href="'.route('admin.users.show', $row->created_by_id).'">' . $img . $name . '</a>' : '';                
                return $str;
            });
            

            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';

                if ( ! empty( $row->attachment ) ) {

                    $attachment = '<p><a href="'. route('admin.home.media-file-download', ['model' => 'ProjectDisscussion', 'field' => 'attachment', 'record_id' => $row->id]) .'" >'.trans('global.project-discussions.attachment').'</a></p>';
                    $description .= $attachment;
                }

                $route = route( 'admin.project_comment.destroy', ['project_id' => $row->project_id, 'id' => $row->id ]);
                $description .= '<p><form method="POST" action="'.$route.'" accept-charset="UTF-8" style="display: inline-block;" onsubmit="return confirm(\''.trans("global.app_are_you_sure").'\');"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="'.csrf_token().'">
    <input class="btn btn-xs btn-danger" type="submit" value="'.trans('global.app_delete').'">
    </form></p>';

                if ( Auth::id() == $row->created_by_id ) {
                    $route_edit = route( 'admin.project_discussions.comments', ['project_id' => $row->project_id, 'discussion_id' => $row->discussion_id, 'operation' => 'edit', 'id' => $row->id ]);
                    $description .= '<a href="'.$route_edit.'">'.trans('global.app_edit').'</a>';
                }

                $route_edit = route( 'admin.project_discussions.comments', ['project_id' => $row->project_id, 'discussion_id' => $row->discussion_id, 'operation' => 'answer', 'id' => $row->id ]);
                $description .= '&nbsp;|&nbsp;<a href="'.$route_edit.'">'.trans('global.project-discussions.answer').'</a>';
                
                return view('admin.project_discussions.comments', compact('row', 'gateKey', 'routeKey'));
            });
            
            $table->editColumn('created_by_id.name', function ($row) {
                return $row->created_name ? $row->created_name->name : '';
            });

            $table->rawColumns(['actions','massDelete','attachment', 'description', 'name']);

            return $table->make(true);
        }

        $topic = ProjectDiscussion::with('created_name')->find( $project_discussion_id );

        $comment = null;
        if ( ! empty( $operation ) && ! empty( $id ) ) {
            $comment = ProjectDiscussionComment::find( $id );
        }

        return view('admin.project_discussions.discussion-comments', compact('project', 'project_discussion_id', 'topic', 'comment', 'operation'));
    }

    /**
     * Show the form for creating new MileStone.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = '' )
    {
        if (! Gate::allows('project_discussion_create')) {
            return abort(401);
        }


        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $show_to_customer = ProjectDiscussion::$show_to_customer;
            
        return view('admin.project_discussions.create', compact('show_to_customer', 'projects', 'project'));
    }

    /**
     * Store a newly created ProjectDiscussion in storage.
     *
     * @param  \App\Http\Requests\StoreProjectDiscussionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectDiscussionsRequest $request, $project_id = '')
    {   
        if (! Gate::allows('project_discussion_create')) {
            return abort(401);
        }
        $additional = array(
            'created_by' => Auth::id(),
        );
        $request->request->add( $additional );
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        ProjectDiscussion::create($request->all());

        flashMessage( 'success', 'create');

        return redirect()->route('admin.project_discussions.index', $project_id);
    }


    /**
     * Show the form for editing ProjectDiscussion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        if (! Gate::allows('project_discussion_edit')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $show_to_customer = ProjectDiscussion::$show_to_customer;
            
        $topic = ProjectDiscussion::findOrFail($id);

        return view('admin.project_discussions.edit', compact('mile_stone', 'show_to_customer', 'projects', 'project', 'topic'));
    }

    /**
     * Update ProjectDiscussion in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectDiscussionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectDiscussionsRequest $request, $project_id, $id)
    {
        if (! Gate::allows('project_discussion_edit')) {
            return abort(401);
        }
        $mile_stone = ProjectDiscussion::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $mile_stone->update($request->all());

        flashMessage( 'success', 'update');

        return redirect()->route('admin.project_discussions.index', $project_id);
    }


    /**
     * Display ProjectDiscussion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $id)
    {
        if (! Gate::allows('project_discussion_view')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $topic = ProjectDiscussion::findOrFail($id);

        return view('admin.project_discussions.show', compact('topic', 'project'));
    }


    /**
     * Remove ProjectDiscussion from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $project_id, $id)
    {
        if (! Gate::allows('project_discussion_delete')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $mile_stone = ProjectDiscussion::findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->delete();

        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.project_discussions.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProjectDiscussion at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('project_discussion_delete')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProjectDiscussion::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore ProjectDiscussion from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($project_id, $id)
    {
        if (! Gate::allows('project_discussion_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $mile_stone = ProjectDiscussion::onlyTrashed()->findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete ProjectDiscussion from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($project_id, $id)
    {
        if (! Gate::allows('project_discussion_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $mile_stone = ProjectDiscussion::onlyTrashed()->findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }

    public function commentsStore(StoreProjectDiscussionCommentsRequest $request, $project_id, $discussion_id, $operation = '', $id = '' ) {

        $message = trans('global.project-discussions.comment-posted');
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }


        if ( ! empty( $operation ) && ! empty( $id ) ) {
            $comment = ProjectDiscussionComment::findOrFail($id);

            if ( 'answer' === $operation ) {
                $additional = array(
                    'created_by_id' => Auth::id(),
                    'project_id' => $project_id,
                    'discussion_id' => $comment->discussion_id,
                    'parent_id' => $id,
                );
                

                ProjectDiscussionComment::create(array_merge( $request->all(), $additional ));

                $message = trans('global.project-discussions.comment-answered');
            } else {                
                $comment->update($request->all());
                $message = trans('global.project-discussions.comment-updated');
            }
        } else {
        
        $data = $request->all();
        $additional = array(
            'created_by_id' => Auth::id(),
            'project_id' => $project_id,
            'discussion_id' => $discussion_id,
        );
        $data = array_merge( $data, $additional );

        ProjectDiscussionComment::create($data);
        }

        $discussion = ProjectDiscussion::find( $discussion_id );
        $discussion->last_activity = Carbon::now();
        $discussion->save();

        flashMessage( 'success', 'create', $message);

        return redirect()->route('admin.project_discussions.comments', [ 'project_id' => $project_id, 'discussion_id' => $discussion_id ]);
    }

    

    public function commentDelete( $project_id, $id ) {
        if (! Gate::allows('project_discussion_delete')) {
            return abort(401);
        }
        $comment = ProjectDiscussionComment::findOrFail($id);
        $discussion_id = $comment->discussion_id;
        $comment->forceDelete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.project_discussions.comments', [ 'project_id' => $project_id, 'discussion_id' => $discussion_id ]);
    }
}