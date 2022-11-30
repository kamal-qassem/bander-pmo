<?php

namespace App\Http\Controllers\Admin;

use App\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncomeCategoriesRequest;
use App\Http\Requests\Admin\UpdateIncomeCategoriesRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class IncomeCategoriesController extends Controller
{
    /**
     * Display a listing of IncomeCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('income_category_access')) {
            return prepareBlockUserMessage();
        }


                $income_categories = IncomeCategory::all()->sortByDesc('id');

        return view('admin.income_categories.index', compact('income_categories'));
    }

    /**
     * Show the form for creating new IncomeCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('income_category_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.income_categories.create');
    }

    /**
     * Store a newly created IncomeCategory in storage.
     *
     * @param  \App\Http\Requests\StoreIncomeCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('income_category_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name'  => 'bail|required|unique:income_categories',
        ];
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

        $income_category = IncomeCategory::create($request->all());
        
        if ( $request->ajax() ) {
            $income_category->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $income_category]);
        } else {
            flashMessage( 'success', 'create' );
        return redirect()->route('admin.income_categories.index');
        }
    }


    /**
     * Show the form for editing IncomeCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('income_category_edit')) {
            return prepareBlockUserMessage();
        }
        $income_category = IncomeCategory::findOrFail($id);

        return view('admin.income_categories.edit', compact('income_category'));
    }

    /**
     * Update IncomeCategory in storage.
     *
     * @param  \App\Http\Requests\UpdateIncomeCategoriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateIncomeCategoriesRequest $request, $id)
    {
        if (! Gate::allows('income_category_edit')) {
            return prepareBlockUserMessage();
        }
        $income_category = IncomeCategory::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $income_category->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.income_categories.index');
    }


    /**
     * Display IncomeCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$list = '')
    {
        if (! Gate::allows('income_category_view')) {
            return prepareBlockUserMessage();
        }
       
        $income_category = IncomeCategory::findOrFail($id);

        return view('admin.income_categories.show', compact('income_category', 'list'));
    }


    /**
     * Remove IncomeCategory from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        
        if (! Gate::allows('income_category_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $income_category = IncomeCategory::findOrFail($id);
        $income_category->delete();

        flashMessage( 'success', 'delete' );
          if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.income_categories.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected IncomeCategory at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('income_category_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = IncomeCategory::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
