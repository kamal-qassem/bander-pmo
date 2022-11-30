<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskTag
 *
 * @package App
 * @property string $name
*/
class TaskTag extends Model
{
    protected $fillable = ['name'];
    protected $hidden = [];
    
    
    public static function boot()
    {
        parent::boot();

        TaskTag::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
