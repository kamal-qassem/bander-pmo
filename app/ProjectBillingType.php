<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProjectBillingType
 *
 * @package App
 * @property string $title
 * @property text $description
*/
class ProjectBillingType extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ProjectBillingType::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
