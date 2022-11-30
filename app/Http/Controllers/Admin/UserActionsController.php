<?php

namespace App\Http\Controllers\Admin;

use App\UserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserActionsRequest;
use App\Http\Requests\Admin\UpdateUserActionsRequest;
use Yajra\DataTables\DataTables;

class UserActionsController extends Controller
{
    /**
     * Display a listing of UserAction.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('user_action_access')) {
            return prepareBlockUserMessage();
        }


    
        if (request()->ajax()) {
            $query = UserAction::query();
            $template = 'actionsTemplate';          
            $query->with("user");
            $query->select([
                'user_actions.id',
                'user_actions.action',
                'user_actions.action_model',
                'user_actions.action_id',
                'user_actions.created_at',
                'user_actions.user_id',
            ]);
            $query->orderBy('id', 'desc');

            if ( ! empty( $type ) && 'user' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('user_actions.user_id', $type_id);
                });
            }

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'truck_';
                $routeKey = 'admin.user_actions';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('user.name', function ($row) {
                return $row->user ? $row->user->name : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? digiDate( $row->created_at, true ) : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.user_actions.index');
    }

    /**
     * Display Truck.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $action = UserAction::findOrFail($id);

        return view('admin.user_actions.show', compact('action'));
    }

    /**
     * Remove Truck from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        
        $truck = UserAction::findOrFail($id);
        $truck->delete();
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.user_actions.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Truck at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if ($request->input('ids')) {
            $entries = UserAction::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }
}
