<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Modules\DynamicOptions\Entities\DynamicOption;
use DB;

/**
 * Class ProjectTask
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property integer $priority
 * @property string $startdate
 * @property string $duedate
 * @property string $datefinished
 * @property integer $status
 * @property string $recurring
 * @property enum $recurring_type
 * @property integer $recurring_value
 * @property integer $cycles
 * @property integer $total_cycles
 * @property string $last_recurring_date
 * @property enum $is_public
 * @property enum $billable
 * @property enum $billed
 * @property string $project
 * @property decimal $hourly_rate
 * @property integer $milestone
 * @property integer $kanban_order
 * @property integer $milestone_order
 * @property enum $visible_to_client
 * @property enum $deadline_notified
 * @property string $mile_stone
 * @property string $created_by
*/
class ProjectTaskTimer extends Model
{

    protected $fillable = ['task_id', 'start_time', 'end_time', 'user_id', 'hourly_rate', 'note',];
    protected $hidden = [];
    public static $searchable = [];
    
    public static function boot()
    {
        parent::boot();

        ProjectTask::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setTaskIdAttribute($input)
    {
        $this->attributes['task_id'] = $input ? $input : null;
    }

    
    /**
     * Set attribute to money format
     * @param $input
     */
    public function setUserIdAttribute($input)
    {
        $this->attributes['user_id'] = $input ? $input : null;
    }

    

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setHourlyRateAttribute($input)
    {
        $this->attributes['hourly_rate'] = $input ? $input : null;
    }

        
    public function task()
    {
        return $this->belongsTo(\App\ProjectTask::class, 'recurring_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
