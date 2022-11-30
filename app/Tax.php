<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Tax
 *
 * @package App
 * @property string $name
 * @property double $rate
 * @property enum $rate_type
 * @property text $description
*/
class Tax extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'rate', 'rate_type', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Tax::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_rate_type = ["value" => "Value", "percent" => "Percent"];

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setRateAttribute($input)
    {
        if ($input != '') {
            $this->attributes['rate'] = $input;
        } else {
            $this->attributes['rate'] = null;
        }
    }
    
}
