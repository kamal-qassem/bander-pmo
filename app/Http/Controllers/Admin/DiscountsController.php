<?php

namespace App\Http\Controllers\Admin;

use App\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountsRequest;
use App\Http\Requests\Admin\UpdateDiscountsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class DiscountsController extends Controller
{
    /**
     * Display a listing of Discount.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('discount_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Discount::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('discount_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'discounts.id',
                'discounts.name',
                'discounts.discount',
                'discounts.discount_type',
                'discounts.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'discount_';
                $routeKey = 'admin.discounts';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount : '';
            });
            $table->editColumn('discount_type', function ($row) {
                return $row->discount_type ? $row->discount_type : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.discounts.index');
    }

    /**
     * Show the form for creating new Discount.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('discount_create')) {
            return prepareBlockUserMessage();
        }        $enum_discount_type = Discount::$enum_discount_type;
            
        return view('admin.discounts.create', compact('enum_discount_type'));
    }

    /**
     * Store a newly created Discount in storage.
     *
     * @param  \App\Http\Requests\StoreDiscountsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('discount_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:discounts,name',
            'discount' => 'numeric|required',
        ];
        if ( 'percent' === $request->discount_type ) {
            $rules['discount'] = 'numeric|max:100';
        }
        
        $validator = Validator::make($request->all(), $rules);
        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $discount = Discount::create($request->all());        

        if ( $request->ajax() ) {
            $discount->selectedid = 'discount_id';
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $discount]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.discounts.index');
        }
    }


    /**
     * Show the form for editing Discount.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('discount_edit')) {
            return prepareBlockUserMessage();
        }        $enum_discount_type = Discount::$enum_discount_type;
            
        $discount = Discount::findOrFail($id);

        return view('admin.discounts.edit', compact('discount', 'enum_discount_type'));
    }

    /**
     * Update Discount in storage.
     *
     * @param  \App\Http\Requests\UpdateDiscountsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDiscountsRequest $request, $id)
    {
        

        if (! Gate::allows('discount_edit')) {
            return prepareBlockUserMessage();
        }

        $discount = Discount::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $discount->update($request->all());

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.discounts.index');
    }


    /**
     * Display Discount.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('discount_view')) {
            return prepareBlockUserMessage();
        }

        $discount = Discount::findOrFail($id);

        return view('admin.discounts.show', compact('discount', 'list'));
    }


    /**
     * Remove Discount from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('discount_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $discount = Discount::findOrFail($id);
        $discount->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.discounts.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Discount at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('discount_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Discount::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Discount from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('discount_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $discount = Discount::onlyTrashed()->findOrFail($id);
        $discount->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Discount from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('discount_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $discount = Discount::onlyTrashed()->findOrFail($id);
        $discount->forceDelete();

        return back();
    }
}
