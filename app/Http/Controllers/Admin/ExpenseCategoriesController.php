<?php

namespace App\Http\Controllers\Admin;

use App\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseCategoriesRequest;
use App\Http\Requests\Admin\UpdateExpenseCategoriesRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class ExpenseCategoriesController extends Controller
{
    /**
     * Display a listing of ExpenseCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('expense_category_access')) {
            return prepareBlockUserMessage();
        }
        
        $expense_categories = ExpenseCategory::all()->sortByDesc('id');

        return view('admin.expense_categories.index', compact('expense_categories'));
    }

    /**
     * Show the form for creating new ExpenseCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('expense_category_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.expense_categories.create');
    }

    /**
     * Store a newly created ExpenseCategory in storage.
     *
     * @param  \App\Http\Requests\StoreExpenseCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('expense_category_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name'  => 'required|unique:expense_categories',
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
        $expense_category = ExpenseCategory::create($request->all());

        if ( $request->ajax() ) {
            $expense_category->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $expense_category]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.expense_categories.index');
        }
    }


    /**
     * Show the form for editing ExpenseCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('expense_category_edit')) {
            return prepareBlockUserMessage();
        }
        $expense_category = ExpenseCategory::findOrFail($id);

        return view('admin.expense_categories.edit', compact('expense_category'));
    }

    /**
     * Update ExpenseCategory in storage.
     *
     * @param  \App\Http\Requests\UpdateExpenseCategoriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpenseCategoriesRequest $request, $id)
    {
        if (! Gate::allows('expense_category_edit')) {
            return prepareBlockUserMessage();
        }
        $expense_category = ExpenseCategory::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $expense_category->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.expense_categories.index');
    }


    /**
     * Display ExpenseCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id , $list = '')
    {
        if (! Gate::allows('expense_category_view')) {
            return prepareBlockUserMessage();
        }
        $expenses = \App\Expense::where('expense_category_id', $id)->get();

        $expense_category = ExpenseCategory::findOrFail($id);

        return view('admin.expense_categories.show', compact('expense_category', 'list'));
    }


    /**
     * Remove ExpenseCategory from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('expense_category_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $expense_category = ExpenseCategory::findOrFail($id);
        $expense_category->delete();


        flashMessage( 'success', 'delete' );
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
     * Delete all selected ExpenseCategory at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('expense_category_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ExpenseCategory::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
