<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskStatus
 *
 * @package App
 * @property string $name
*/
class TaskStatus extends Model
{
    protected $fillable = ['name', 'color'];
    protected $hidden = [];
    
    
    public static function boot()
    {
        parent::boot();

        TaskStatus::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
