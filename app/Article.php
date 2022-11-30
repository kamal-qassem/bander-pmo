<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Article
 *
 * @package App
 * @property string $title
 * @property text $page_text
 * @property text $excerpt
 * @property string $featured_image
*/
class Article extends Model
{
    protected $fillable = ['title', 'page_text', 'excerpt', 'featured_image'];
    protected $hidden = [];
    public static $searchable = [ 'title', 'page_text' ];
    
    public static function boot()
    {
        parent::boot();

        Article::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
    public function category_id()
    {
        return $this->belongsToMany(ContentCategory::class, 'article_content_category');
    }
    
    public function tag_id()
    {
        return $this->belongsToMany(ContentTag::class, 'article_content_tag');
    }
    
    public function available_for()
    {
        return $this->belongsToMany(Role::class, 'article_role');
    }
    
}
