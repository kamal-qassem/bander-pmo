<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Country
 *
 * @package App
 * @property string $shortcode
 * @property string $title
*/
class Country extends Model
{
    use SoftDeletes;

    protected $fillable = ['shortcode', 'title', 'dialcode'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Country::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
