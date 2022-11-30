<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class MasterSetting
 *
 * @package App
 * @property string $module
 * @property string $key
 * @property text $description
*/
class MasterSetting extends Model
{
    use SoftDeletes;
    use HasSlug;

    protected $fillable = ['module', 'key', 'description', 'slug', 'settings_data', 'moduletype', 'status'];
    protected $hidden = [];
    public static $searchable = [ 'module', 'description'
    ];

    public static $enum_moduletype = ['general' => 'General', "payment" => "Payment Gateway", "sms" => "SMS Gateway" ];
    
    public static function boot()
    {
        parent::boot();

        MasterSetting::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('key')
            ->saveSlugsTo('slug');
    }
    
}
