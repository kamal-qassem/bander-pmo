<?php

namespace App\Http\Controllers\Admin;

use App\FaqQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqQuestionsRequest;
use App\Http\Requests\Admin\UpdateFaqQuestionsRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class FaqQuestionsController extends Controller
{   

    public function __construct() {
       $this->middleware('plugin:knowledge_base');
    }

    /**
     * Display a listing of FaqQuestion.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('faq_question_access')) {
            return prepareBlockUserMessage();
        }


                $faq_questions = FaqQuestion::all();

        return view('admin.faq_questions.index', compact('faq_questions'));
    }

    /**
     * Show the form for creating new FaqQuestion.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('faq_question_create')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\FaqCategory::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.faq_questions.create', compact('categories'));
    }

    /**
     * Store a newly created FaqQuestion in storage.
     *
     * @param  \App\Http\Requests\StoreFaqQuestionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFaqQuestionsRequest $request)
    {
        if (! Gate::allows('faq_question_create')) {
            return prepareBlockUserMessage();
        }
        $faq_question = FaqQuestion::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.faq_questions.index');
    }


    /**
     * Show the form for editing FaqQuestion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('faq_question_edit')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\FaqCategory::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $faq_question = FaqQuestion::findOrFail($id);

        return view('admin.faq_questions.edit', compact('faq_question', 'categories'));
    }

    /**
     * Update FaqQuestion in storage.
     *
     * @param  \App\Http\Requests\UpdateFaqQuestionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFaqQuestionsRequest $request, $id)
    {
        if (! Gate::allows('faq_question_edit')) {
            return prepareBlockUserMessage();
        }
        $faq_question = FaqQuestion::findOrFail($id);
        $faq_question->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.faq_questions.index');
    }


    /**
     * Display FaqQuestion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('faq_question_view')) {
            return prepareBlockUserMessage();
        }
        $faq_question = FaqQuestion::findOrFail($id);

        return view('admin.faq_questions.show', compact('faq_question'));
    }


    /**
     * Remove FaqQuestion from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('faq_question_delete')) {
            return prepareBlockUserMessage();
        }
        $faq_question = FaqQuestion::findOrFail($id);
        $faq_question->delete();

        flashMessage( 'success', 'delete' );
      if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.faq_questions.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected FaqQuestion at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('faq_question_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = FaqQuestion::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
