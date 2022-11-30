<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRolesRequest;
use App\Http\Requests\Admin\UpdateRolesRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class RolesController extends Controller
{
    /**
     * Display a listing of Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('role_access')) {
            return prepareBlockUserMessage();
        }


        $roles = Role::where('type','role')->get()->sortByDesc('id');

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating new Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('role_create')) {
            return prepareBlockUserMessage();
        }
        
        $permissions = \App\Permission::get()->pluck('title', 'id');


        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param  \App\Http\Requests\StoreRolesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRolesRequest $request)
    {
        if (! Gate::allows('role_create')) {
            return prepareBlockUserMessage();
        }

        $addtional = array(
            'type' => 'role',
            'name' => $request->title,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $role = Role::create($request->all());
        $role->permission()->sync(array_filter((array)$request->input('permission')));


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.roles.index');
    }


    /**
     * Show the form for editing Role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('role_edit')) {
            return prepareBlockUserMessage();
        }
        
        $permissions = \App\Permission::get()->pluck('title', 'id');


        $role = Role::findOrFail($id);

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update Role in storage.
     *
     * @param  \App\Http\Requests\UpdateRolesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRolesRequest $request, $id)
    {
        if (! Gate::allows('role_edit')) {
            return prepareBlockUserMessage();
        }
        $role = Role::findOrFail($id);

        $addtional = array(
            'name' => $request->title,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $role->update($request->all());
        $role->permission()->sync(array_filter((array)$request->input('permission')));


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.roles.index');
    }


    /**
     * Display Role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('role_view')) {
            return prepareBlockUserMessage();
    }
        
        $permissions = \App\Permission::get()->pluck('title', 'id');

        $role = Role::findOrFail($id);

        return view('admin.roles.show', compact('role', 'list'));
    }


    /**
     * Remove Role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('role_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $role = Role::findOrFail($id);
        $role->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.roles.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Role at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('role_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Role::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
        flashMessage( 'success', 'deletes' );
    }

}
