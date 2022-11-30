<?php

namespace App\Http\Controllers\Admin;

use App\ContactNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactNotesRequest;
use App\Http\Requests\Admin\UpdateContactNotesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ContactNotesController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ContactNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $contact_id = '' )
    {
        if (! Gate::allows('contact_note_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = ContactNote::query();
            $query->with("contact");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contact_note_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contact_notes.id',
                'contact_notes.title',
                'contact_notes.contact_id',
                'contact_notes.notes',
            ]);

            $query->when($contact_id, function ($q, $contact_id) { 
                return $q->where('contact_notes.contact_id', $contact_id);
            });
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contact_note_';
                $routeKey = 'admin.contact_notes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('contact.first_name', function ($row) {
                return $row->contact ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->contact->id, 'list' => 'contact_notes']).'">' . $row->contact->name : '';
            });
            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : '';
            });
            
            $table->editColumn('attachment', function ($row) {
                $build  = '';
                foreach ($row->getMedia('attachment') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '" >' . $media->name . '</a></p>';
                }
                
                return $build;
            });

            $table->rawColumns(['actions','massDelete','attachment', 'contact.first_name']);

            return $table->make(true);
        }

        return view('admin.contact_notes.index');
    }

    /**
     * Show the form for creating new ContactNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contact_note_create')) {
            return prepareBlockUserMessage();
        }
        
        $contacts = \App\Contact::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.contact_notes.create', compact('contacts'));
    }

    /**
     * Store a newly created ContactNote in storage.
     *
     * @param  \App\Http\Requests\StoreContactNotesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContactNotesRequest $request)
    {
        if (! Gate::allows('contact_note_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_note = ContactNote::create($request->all());


        foreach ($request->input('attachment_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $contact_note->id;
            $file->save();
        }

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.contact_notes.index');
    }


    /**
     * Show the form for editing ContactNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contact_note_edit')) {
            return prepareBlockUserMessage();
        }
        
        $contacts = \App\Contact::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $contact_note = ContactNote::findOrFail($id);

        return view('admin.contact_notes.edit', compact('contact_note', 'contacts'));
    }

    /**
     * Update ContactNote in storage.
     *
     * @param  \App\Http\Requests\UpdateContactNotesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactNotesRequest $request, $id)
    {

        if (! Gate::allows('contact_note_edit')) {
            return prepareBlockUserMessage();
        }

        if ( ! isDemo() ) {
         $request = $this->saveFiles($request);
        }
     
        
        $contact_note = ContactNote::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_note->update($request->all());


        $media = [];
        foreach ($request->input('attachment_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $contact_note->id;
            $file->save();
            $media[] = $file->toArray();
        }



        $contact_note->updateMedia($media, 'attachment');

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.contact_notes.index');
    }


    /**
     * Display ContactNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('contact_note_view')) {
            return prepareBlockUserMessage();
        }
        $contact_note = ContactNote::findOrFail($id);
        

        return view('admin.contact_notes.show', compact('contact_note'));
    }


    /**
     * Remove ContactNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('contact_note_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_note = ContactNote::findOrFail($id);
        $contact_note->deletePreservingMedia();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.contact_notes.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        } // We are deleting records from different pages, so let us back to the same page.
     }
    }

    /**
     * Delete all selected ContactNote at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contact_note_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContactNote::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ContactNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contact_note_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_note = ContactNote::onlyTrashed()->findOrFail($id);
        $contact_note->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ContactNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contact_note_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_note = ContactNote::onlyTrashed()->findOrFail($id);
        $contact_note->forceDelete();

        return back();
    }
}
