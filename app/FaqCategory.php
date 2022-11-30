<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FaqCategory
 *
 * @package App
 * @property string $title
*/
class FaqCategory extends Model
{
    protected $fillable = ['title'];
    protected $hidden = [];
    
    
    public static function boot()
    {
        parent::boot();

        FaqCategory::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
