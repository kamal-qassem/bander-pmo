<?php

namespace App\Http\Controllers\Admin;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandsRequest;
use App\Http\Requests\Admin\UpdateBrandsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class BrandsController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
       $this->middleware('plugin:productbrand');
    }
    /**
     * Display a listing of Brand.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('brand_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Brand::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('brand_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'brands.id',
                'brands.title',
                'brands.icon',
                'brands.status',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'brand_';
                $routeKey = 'admin.brands';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('icon', function ($row) {
                if($row->icon && file_exists(public_path() . '/thumb/' . $row->icon)) { 

                    return '<a href="'. asset(env('UPLOAD_PATH').'/' . $row->icon) .'" target="_blank"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->icon) .'"/>'; 
                }
                else{
                    
                    return '<img src="'. asset('images/product-50x50.jpg') .'" title="'.$row->title.'" width="50" height="50"/>';
                }
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });

            $table->rawColumns(['actions','massDelete','icon']);

            return $table->make(true);
        }

        return view('admin.brands.index');
    }

    /**
     * Show the form for creating new Brand.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('brand_create')) {
            return prepareBlockUserMessage();
        }        $enum_status = Brand::$enum_status;
            
        return view('admin.brands.create', compact('enum_status'));
    }

    /**
     * Store a newly created Brand in storage.
     *
     * @param  \App\Http\Requests\StoreBrandsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('brand_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'title' => 'required|unique:brands,title',
            'icon' => 'nullable|mimes:png,jpg,jpeg,gif',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $brand = Brand::create($request->all());

        
        if ( $request->ajax() ) {
            $brand->selectedid = 'brand_id';
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $brand]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.brands.index');
        }
    }


    /**
     * Show the form for editing Brand.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('brand_edit')) {
            return prepareBlockUserMessage();
        }        $enum_status = Brand::$enum_status;
            
        $brand = Brand::findOrFail($id);

        return view('admin.brands.edit', compact('brand', 'enum_status'));
    }

    /**
     * Update Brand in storage.
     *
     * @param  \App\Http\Requests\UpdateBrandsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBrandsRequest $request, $id)
    {
        if (! Gate::allows('brand_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $brand = Brand::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $brand->update($request->all());

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.brands.index');
    }


    /**
     * Display Brand.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$list = '')
    {
        if (! Gate::allows('brand_view')) {
            return prepareBlockUserMessage();
        }

        $brand = Brand::findOrFail($id);

        return view('admin.brands.show', compact('brand', 'list'));
    }


    /**
     * Remove Brand from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('brand_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $brand = Brand::findOrFail($id);
        $brand->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.brands.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Brand at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('brand_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Brand::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Brand from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('brand_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $brand = Brand::onlyTrashed()->findOrFail($id);
        $brand->restore();

        flashMessage( 'success', 'restore' );

        return back();
    }

    /**
     * Permanently delete Brand from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('brand_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $brand = Brand::onlyTrashed()->findOrFail($id);
        $brand->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }
}
