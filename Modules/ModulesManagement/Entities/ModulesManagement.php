<?php
namespace Modules\ModulesManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ModulesManagement
 *
 * @package App
 * @property string $name
 * @property string $slug
 * @property enum $type
 * @property enum $enabled
 * @property text $description
*/
class ModulesManagement extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'type', 'enabled', 'description', 'settings_data', 'can_inactive'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ModulesManagement::observe(new \App\Observers\UserActionsObserver);
    }

    public static $enum_type = ["Core" => "Core", "Custom" => "Custom"];

    public static $enum_enabled = ["Yes" => "Yes", "No" => "No"];
    
}
