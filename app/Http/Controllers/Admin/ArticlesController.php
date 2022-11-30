<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticlesRequest;
use App\Http\Requests\Admin\UpdateArticlesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ArticlesController extends Controller
{   

    public function __construct() {
        $this->middleware('plugin:article');
    }
    use FileUploadTrait;

    /**
     * Display a listing of Article.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '',$catid = 0 )
    {
        if (! Gate::allows('article_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Article::query();
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
            $query->with("available_for");
            
            $template = 'actionsTemplate';
            
            $query->select([
                'articles.id',
                'articles.title',
                'articles.page_text',
                'articles.excerpt',
                'articles.featured_image',
                
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
                $routeKey = 'admin.articles';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? '<a href="'.route('admin.articles.show', $row->id).'">' . $row->title . '</a>' : '';
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
               if($row->featured_image && file_exists(public_path() . '/thumb/' . $row->featured_image))  { 
                return '<a href="'. route('admin.home.media-file-download', [ 'model' => 'Article', 'field' => 'featured_image', 'record_id' => $row->id ]) .'"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->featured_image) .'"/></a>'; 
                   }
                   else
                   {
                        return '<img src="'. asset('images/product-50x50.jpg') .'" title="'.$row->title.'" width="50" height="50"/>';
                   }
            });
            $table->editColumn('available_for.title', function ($row) {
                if(count($row->available_for) == 0) {
                    return '';
                }

                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->available_for->pluck('title')->toArray()) . '</span>';
            });

            $table->rawColumns(['title', 'actions','massDelete','category_id.title','tag_id.title','featured_image','available_for.title', 'page_text']);

            return $table->make(true);
        }

        if ( 'articles' === $type ) {
            $catid = 0;
        }

        return view('admin.articles.index', compact('type', 'type_id','catid'));
    }

    /**
     * Show the form for creating new Article.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('article_create')) {
            return prepareBlockUserMessage();
        }
        
        $category_ids = \App\ContentCategory::get()->pluck('title', 'id');

        $tag_ids = \App\ContentTag::get()->pluck('title', 'id');

        $available_fors = \App\Role::get()->pluck('title', 'id');


        return view('admin.articles.create', compact('category_ids', 'tag_ids', 'available_fors'));
    }

    /**
     * Store a newly created Article in storage.
     *
     * @param  \App\Http\Requests\StoreArticlesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreArticlesRequest $request)
    {
        if (! Gate::allows('article_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $article = Article::create($request->all());
        $article->category_id()->sync(array_filter((array)$request->input('category_id')));
        $article->tag_id()->sync(array_filter((array)$request->input('tag_id')));
        $article->available_for()->sync(array_filter((array)$request->input('available_for')));


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.articles.index');
    }


    /**
     * Show the form for editing Article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('article_edit')) {
            return prepareBlockUserMessage();
        }
        
        $category_ids = \App\ContentCategory::get()->pluck('title', 'id');

        $tag_ids = \App\ContentTag::get()->pluck('title', 'id');

        $available_fors = \App\Role::get()->pluck('title', 'id');


        $article = Article::findOrFail($id);

        return view('admin.articles.edit', compact('article', 'category_ids', 'tag_ids', 'available_fors'));
    }

    /**
     * Update Article in storage.
     *
     * @param  \App\Http\Requests\UpdateArticlesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateArticlesRequest $request, $id)
    {
        if (! Gate::allows('article_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
         }
        $article = Article::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $article->update($request->all());
        $article->category_id()->sync(array_filter((array)$request->input('category_id')));
        $article->tag_id()->sync(array_filter((array)$request->input('tag_id')));
        $article->available_for()->sync(array_filter((array)$request->input('available_for')));


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.articles.index');
    }


    /**
     * Display Article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('article_view')) {
            return prepareBlockUserMessage();
        }
        
        $article = Article::findOrFail($id);

        return view('admin.articles.show', compact('article'));
    }


    /**
     * Remove Article from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('article_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $article = Article::findOrFail($id);
        $article->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.articles.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
       } 
    }

    /**
     * Delete all selected Article at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('article_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Article::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
