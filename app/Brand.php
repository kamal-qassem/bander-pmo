<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Brand
 *
 * @package App
 * @property string $title
 * @property string $icon
 * @property enum $status
*/
class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'icon', 'status'];
    protected $hidden = [];
    public static $searchable = [ 'title' ];
    
    public static function boot()
    {
        parent::boot();

        Brand::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    
}
