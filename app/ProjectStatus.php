<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProjectStatus
 *
 * @package App
 * @property string $name
 * @property text $description
*/
class ProjectStatus extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ProjectStatus::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
