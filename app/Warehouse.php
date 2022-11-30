<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Warehouse
 *
 * @package App
 * @property string $name
 * @property text $address
 * @property text $description
*/
class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'description'];
    protected $hidden = [];
    public static $searchable = [ 'name', 'address', 'description'
    ];
    
    public static function boot()
    {
        parent::boot();

        Warehouse::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
