<?php

namespace App\Http\Controllers\Admin;

use App\TaskTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskTagsRequest;
use App\Http\Requests\Admin\UpdateTaskTagsRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class TaskTagsController extends Controller
{
    /**
     * Display a listing of TaskTag.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('task_tag_access')) {
            return prepareBlockUserMessage();
        }


                $task_tags = TaskTag::all()->sortByDesc('id');

        return view('admin.task_tags.index', compact('task_tags'));
    }

    /**
     * Show the form for creating new TaskTag.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('task_tag_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.task_tags.create');
    }

    /**
     * Store a newly created TaskTag in storage.
     *
     * @param  \App\Http\Requests\StoreTaskTagsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskTagsRequest $request)
    {
        if (! Gate::allows('task_tag_create')) {
            return prepareBlockUserMessage();
        }
        $task_tag = TaskTag::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.task_tags.index');
    }


    /**
     * Show the form for editing TaskTag.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('task_tag_edit')) {
            return prepareBlockUserMessage();
        }
        $task_tag = TaskTag::findOrFail($id);

        return view('admin.task_tags.edit', compact('task_tag'));
    }

    /**
     * Update TaskTag in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskTagsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskTagsRequest $request, $id)
    {
        if (! Gate::allows('task_tag_edit')) {
            return prepareBlockUserMessage();
        }
        $task_tag = TaskTag::findOrFail($id);
        $task_tag->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.task_tags.index');
    }


    /**
     * Display TaskTag.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('task_tag_view')) {
            return prepareBlockUserMessage();
        }
        $tasks = \App\Task::whereHas('tag',
                    function ($query) use ($id) {
                        $query->where('id', $id);
                    })->get();

        $task_tag = TaskTag::findOrFail($id);

        return view('admin.task_tags.show', compact('task_tag', 'tasks'));
    }


    /**
     * Remove TaskTag from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('task_tag_delete')) {
            return prepareBlockUserMessage();
        }
        $task_tag = TaskTag::findOrFail($id);
        $task_tag->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.task_tags.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected TaskTag at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('task_tag_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = TaskTag::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
