<?php
namespace Modules\SiteThemes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SiteTheme
 *
 * @package App
 * @property string $title
 * @property string $slug
 * @property string $theme_title_key
 * @property text $settings_data
 * @property string $description
 * @property enum $is_active
 * @property string $theme_color
*/
class SiteTheme extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'theme_title_key', 'settings_data', 'description', 'is_active', 'theme_color'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        SiteTheme::observe(new \App\Observers\UserActionsObserver);
    }

    public static $enum_is_active = ["1" => "Active", "0" => "Inactive"];

    /**
     * This method validates and sends the setting value
     * @param  [type] $setting_type [description]
     * @param  [type] $key          [description]
     * @return [type]               [description]
     */
    public static function getSetting($key, $setting_module, $default = '')
    {

        $setting_module     = strtolower($setting_module);

        $setting_record = SiteTheme::where('slug', $setting_module)->first();

        if ( ! $setting_record ) {
            return $default;
        }

        $settings = (array) json_decode( $setting_record->settings_data );

        if(!array_key_exists($key, $settings))
        {
            return 'invalid_setting';
        }
        return $settings[ $key ]->value;
    }
    
}
