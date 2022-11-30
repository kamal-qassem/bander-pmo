<?php

namespace App\Http\Controllers\Admin;

use App\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContentPagesRequest;
use App\Http\Requests\Admin\UpdateContentPagesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
class ContentPagesController extends Controller
{
    use FileUploadTrait;
    public function __construct() {
        $this->middleware('plugin:content_management');
    }
    /**
     * Display a listing of ContentPage.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '',$catid = 0  )
    {
        if (! Gate::allows('content_page_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = ContentPage::query();
            $query->with("category_id");

            $type = request('type');
            $catid = request('catid');
            if ( ! empty( $catid ) && $catid > 0 ) {
                if ( 'categories' === $type ) {
                    $query->whereHas("category_id",
                    function ($query) use($catid) {
                        $query->where('id', [$catid]);
                    });
                }
                if ( 'tags' === $type ) {
                    $query->whereHas("tag_id",
                    function ($query) use($catid) {
                        $query->where('id', [$catid]);
                    });
                }
            }

            $query->with("tag_id");
            
            
            $template = 'actionsTemplate';
            
            $query->select([
                'content_pages.id',
                'content_pages.title',
                'content_pages.page_text',
                'content_pages.excerpt',
                'content_pages.featured_image',
                'content_pages.created_at',
                
            ]);

        
            if ( ! empty( $type ) && 'categories' === $type ) {
                $query->whereHas("category_id",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'tags' === $type ) {
                $query->whereHas("tag_id",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                });
            }
            
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'article_';
                $routeKey = 'admin.content_pages';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? '<a href="'.route('admin.content_pages.show', $row->id).'">' . $row->title . '</a>' : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? digiDate( $row->created_at, true ) : '';
            });
            $table->editColumn('category_id.title', function ($row) {
                if(count($row->category_id) == 0) {
                    return '';
                }

                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->category_id->pluck('title')->toArray()) . '</span>';
            });
            $table->editColumn('tag_id.title', function ($row) {
                if(count($row->tag_id) == 0) {
                    return '';
                }

                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->tag_id->pluck('title')->toArray()) . '</span>';
            });
            $table->editColumn('page_text', function ($row) {
                return $row->page_text ? $row->page_text : '';
            });
         
            $table->editColumn('featured_image', function ($row) {
                 if($row->featured_image && file_exists(public_path() . '/thumb/' . $row->featured_image)) { 
                    return '<a href="'. route('admin.home.media-file-download', [ 'model' => 'ContentPage', 'field' => 'featured_image', 'record_id' => $row->id ]) .'"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->featured_image) .'"/></a>'; 
                }
                else
                {
                    return '<img src="'. asset('images/product-50x50.jpg') .'" title="'.$row->title.'" width="50" height="50"/>';
                }
            });
            
            $table->rawColumns(['title', 'actions','massDelete','category_id.title','tag_id.title','featured_image', 'page_text']);

            return $table->make(true);
        }

        if ( 'articles' === $type ) {
            $catid = 0;
        }
        return view('admin.content_pages.index', compact('type', 'catid'));
    }

    /**
     * Show the form for creating new ContentPage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('content_page_create')) {
            return prepareBlockUserMessage();
        }
        
        $category_ids = \App\ContentCategory::get()->pluck('title', 'id');

        $tag_ids = \App\ContentTag::get()->pluck('title', 'id');


        return view('admin.content_pages.create', compact('category_ids', 'tag_ids'));
    }

    /**
     * Store a newly created ContentPage in storage.
     *
     * @param  \App\Http\Requests\StoreContentPagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContentPagesRequest $request)
    {
        if (! Gate::allows('content_page_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $content_page = ContentPage::create($request->all());
        $content_page->category_id()->sync(array_filter((array)$request->input('category_id')));
        $content_page->tag_id()->sync(array_filter((array)$request->input('tag_id')));


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.content_pages.index');
    }


    /**
     * Show the form for editing ContentPage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('content_page_edit')) {
            return prepareBlockUserMessage();
        }
        
        $category_ids = \App\ContentCategory::get()->pluck('title', 'id');

        $tag_ids = \App\ContentTag::get()->pluck('title', 'id');


        $content_page = ContentPage::findOrFail($id);

        return view('admin.content_pages.edit', compact('content_page', 'category_ids', 'tag_ids'));
    }

    /**
     * Update ContentPage in storage.
     *
     * @param  \App\Http\Requests\UpdateContentPagesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContentPagesRequest $request, $id)
    {
        if (! Gate::allows('content_page_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $content_page = ContentPage::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $content_page->update($request->all());
        $content_page->category_id()->sync(array_filter((array)$request->input('category_id')));
        $content_page->tag_id()->sync(array_filter((array)$request->input('tag_id')));


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.content_pages.index');
    }


    /**
     * Display ContentPage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id , $list='')
    {
       
        if (! Gate::allows('content_page_view')) {
            return prepareBlockUserMessage();
        }
        
        $content_page = ContentPage::findOrFail($id);


        return view('admin.content_pages.show', compact('content_page','catid'));
    }


    /**
     * Remove ContentPage from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('content_page_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $content_page = ContentPage::findOrFail($id);
        $content_page->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.content_pages.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ContentPage at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('content_page_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContentPage::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
