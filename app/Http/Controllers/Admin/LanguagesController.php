<?php

namespace App\Http\Controllers\Admin;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLanguagesRequest;
use App\Http\Requests\Admin\UpdateLanguagesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class LanguagesController extends Controller
{   
    public function __construct() {
       $this->middleware('plugin:languages');
    }
    /**
     * Display a listing of Language.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('language_access')) {
            return prepareBlockUserMessage();
        }

        $locals = config('app.languages');
        if ( ! empty( $locals ) ) {
            foreach ($locals as $key => $value) {
                $local = Language::firstOrNew([
                    'code' => $key,
                ]);
                if ( empty( $local->language ) ) {
                    $local->language = $value;
                }
                $local->save();
            }            
        }
                
        if (request()->ajax()) {
            $query = Language::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('language_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'languages.id',
                'languages.language',
                'languages.code',
                'languages.is_rtl',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'language_';
                $routeKey = 'admin.languages';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.languages.index');
    }

    /**
     * Show the form for creating new Language.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('language_create')) {
            return prepareBlockUserMessage();
        }        $enum_is_rtl = Language::$enum_is_rtl;
            
        return view('admin.languages.create', compact('enum_is_rtl'));
    }

    /**
     * Store a newly created Language in storage.
     *
     * @param  \App\Http\Requests\StoreLanguagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLanguagesRequest $request)
    {
        if (! Gate::allows('language_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $language = Language::create($request->all());


        updateLanguages();

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.languages.index');
    }


    /**
     * Show the form for editing Language.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('language_edit')) {
            return prepareBlockUserMessage();
        }        $enum_is_rtl = Language::$enum_is_rtl;
            
        $language = Language::findOrFail($id);

        return view('admin.languages.edit', compact('language', 'enum_is_rtl'));
    }

    /**
     * Update Language in storage.
     *
     * @param  \App\Http\Requests\UpdateLanguagesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLanguagesRequest $request, $id)
    {
        if (! Gate::allows('language_edit')) {
            return prepareBlockUserMessage();
        }
        $language = Language::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $language->update($request->all());

        updateLanguages();

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.languages.index');
    }


    /**
     * Display Language.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('language_view')) {
            return prepareBlockUserMessage();
        }
        $contacts = \App\Contact::whereHas('language',
                    function ($query) use ($id) {
                        $query->where('id', $id);
                    })->get();

        $language = Language::findOrFail($id);

        return view('admin.languages.show', compact('language', 'contacts'));
    }


    /**
     * Remove Language from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('language_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $language = Language::findOrFail($id);
        $language->delete();

        updateLanguages();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.languages.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Language at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('language_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Language::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }

        updateLanguages();
    }


    /**
     * Restore Language from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('language_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $language = Language::onlyTrashed()->findOrFail($id);
        $language->restore();

        updateLanguages();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Language from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('language_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $language = Language::onlyTrashed()->findOrFail($id);
        $language->forceDelete();

        updateLanguages();

        flashMessage( 'success', 'delete' );

        return back();
    }

    /**
     * Permanently delete Language from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeDirection($id)
    {
        if (! Gate::allows('language_edit')) {
            return prepareBlockUserMessage();
        }
        $language = Language::findOrFail($id);
        if ( 'Yes' === $language->is_rtl ) {
            $language->is_rtl = 'No';
        } else {
            $language->is_rtl = 'Yes';
        }
        $language->save();

        updateLanguages();
        
        flashMessage( 'success', 'update' );

        return redirect()->route('admin.languages.index');
    }
}
