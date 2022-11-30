<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MeasurementUnit
 *
 * @package App
 * @property string $title
 * @property enum $status
 * @property text $description
*/
class MeasurementUnit extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'status', 'description'];
    protected $hidden = [];
    public static $searchable = [
        'title',
    ];
    
    public static function boot()
    {
        parent::boot();

        MeasurementUnit::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    
}
