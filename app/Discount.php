<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Discount
 *
 * @package App
 * @property string $name
 * @property double $discount
 * @property enum $discount_type
 * @property text $description
*/
class Discount extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'discount', 'discount_type', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Discount::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_discount_type = ["percent" => "Percent", "value" => "Value"];

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setDiscountAttribute($input)
    {
        if ($input != '') {
            $this->attributes['discount'] = $input;
        } else {
            $this->attributes['discount'] = null;
        }
    }
    
}
