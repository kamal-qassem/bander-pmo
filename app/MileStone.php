<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MileStone
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property enum $description_visible_to_customer
 * @property string $due_date
 * @property string $project
 * @property string $color
 * @property integer $milestone_order
*/
class MileStone extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'description_visible_to_customer', 'due_date', 'color', 'milestone_order', 'project_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        MileStone::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $enum_description_visible_to_customer = ["yes" => "Yes", "no" => "No"];

    

    /**
     * Set to null if empty
     * @param $input
     */
    public function setProjectIdAttribute($input)
    {
        $this->attributes['project_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setMilestoneOrderAttribute($input)
    {
        $this->attributes['milestone_order'] = $input ? $input : null;
    }
    
    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'project_id')->withTrashed();
    }
    
}
