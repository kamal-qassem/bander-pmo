<?php

namespace App\Http\Controllers\Admin;

use App\ContactDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactDocumentsRequest;
use App\Http\Requests\Admin\UpdateContactDocumentsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ContactDocumentsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ContactDocument.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $contact_id = '' )
    {
        if (! Gate::allows('contact_document_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = ContactDocument::query();
            $query->with("contact");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contact_document_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contact_documents.id',
                'contact_documents.name',
                'contact_documents.description',
                'contact_documents.contact_id',
            ]);

            $query->when($contact_id, function ($q, $contact_id) { 
                return $q->where('contact_documents.contact_id', $contact_id);
            });
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contact_document_';
                $routeKey = 'admin.contact_documents';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('attachment', function ($row) {
                $build  = '';
                foreach ($row->getMedia('attachment') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '" >' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('contact.name', function ($row) {
                return $row->contact ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->contact->id, 'list' => 'contact_documents']).'">' . $row->contact->name : '';
            });


            $table->rawColumns(['actions','massDelete','attachment', 'contact.name']);

            return $table->make(true);
        }

        return view('admin.contact_documents.index');
    }

    /**
     * Show the form for creating new ContactDocument.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contact_document_create')) {
            return prepareBlockUserMessage();
        }
        
        $contacts = \App\Contact::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.contact_documents.create', compact('contacts'));
    }

    /**
     * Store a newly created ContactDocument in storage.
     *
     * @param  \App\Http\Requests\StoreContactDocumentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContactDocumentsRequest $request)
    {
        if (! Gate::allows('contact_document_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_document = ContactDocument::create($request->all());


        foreach ($request->input('attachment_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $contact_document->id;
            $file->save();
        }

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.contact_documents.index');
    }


    /**
     * Show the form for editing ContactDocument.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contact_document_edit')) {
            return prepareBlockUserMessage();
        }
        
        $contacts = \App\Contact::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $contact_document = ContactDocument::findOrFail($id);

        return view('admin.contact_documents.edit', compact('contact_document', 'contacts'));
    }

    /**
     * Update ContactDocument in storage.
     *
     * @param  \App\Http\Requests\UpdateContactDocumentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactDocumentsRequest $request, $id)
    {
        if (! Gate::allows('contact_document_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
         }
        $contact_document = ContactDocument::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_document->update($request->all());


        $media = [];
        foreach ($request->input('attachment_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $contact_document->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $contact_document->updateMedia($media, 'attachment');

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.contact_documents.index');
    }


    /**
     * Display ContactDocument.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('contact_document_view')) {
            return prepareBlockUserMessage();
        }
        $contact_document = ContactDocument::findOrFail($id);

        return view('admin.contact_documents.show', compact('contact_document'));
    }


    /**
     * Remove ContactDocument from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('contact_document_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_document = ContactDocument::findOrFail($id);
        $contact_document->deletePreservingMedia();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.contact_documents.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        } // We are deleting records from different pages, so let us back to the same page.
     }
    }

    /**
     * Delete all selected ContactDocument at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contact_document_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContactDocument::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ContactDocument from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contact_document_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_document = ContactDocument::onlyTrashed()->findOrFail($id);
        $contact_document->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ContactDocument from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contact_document_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_document = ContactDocument::onlyTrashed()->findOrFail($id);
        $contact_document->forceDelete();

        return back();
    }
}
