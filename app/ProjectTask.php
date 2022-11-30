<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
//use App\Traits\FilterByUser;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
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
class ProjectTask extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $fillable = ['name', 'description', 'priority', 'startdate', 'duedate', 'datefinished', 'status', 'recurring_type', 'recurring_value', 'cycles', 'total_cycles', 'last_recurring_date', 'is_public', 'billable', 'billed', 'hourly_rate', 'milestone', 'kanban_order', 'milestone_order', 'visible_to_client', 'deadline_notified', 'recurring_id', 'project_id', 'mile_stone_id', 'created_by_id'];
    protected $hidden = [];
    public static $searchable = [
        'name',
    ];
    
    public static function boot()
    {
        parent::boot();

        ProjectTask::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);

        //config(['app.date_format' => env('DATE_FORMAT')]);
    }

    public static $enum_recurring_type = ["day" => "Day", "week" => "Week", "month" => "Month", "year" => "Year"];

    public static $enum_is_public = ["yes" => "Yes", "no" => "No"];

    public static $enum_billable = ["yes" => "Yes", "no" => "No"];

    public static $enum_billed = ["no" => "No", "yes" => "Yes", ];

    public static $enum_visible_to_client = ["yes" => "Yes", "no" => "No"];

    public static $enum_deadline_notified = ["yes" => "Yes", "no" => "No"];

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setPriorityAttribute($input)
    {
        $this->attributes['priority'] = $input ? $input : null;
    }

    

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setStatusAttribute($input)
    {
        $this->attributes['status'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setRecurringIdAttribute($input)
    {
        $this->attributes['recurring_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setRecurringValueAttribute($input)
    {
        $this->attributes['recurring_value'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setCyclesAttribute($input)
    {
        $this->attributes['cycles'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setTotalCyclesAttribute($input)
    {
        $this->attributes['total_cycles'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setLastRecurringDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['last_recurring_date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['last_recurring_date'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getLastRecurringDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
    }

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
    public function setHourlyRateAttribute($input)
    {
        $this->attributes['hourly_rate'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setMilestoneAttribute($input)
    {
        $this->attributes['milestone'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setKanbanOrderAttribute($input)
    {
        $this->attributes['kanban_order'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setMilestoneOrderAttribute($input)
    {
        $this->attributes['milestone_order'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setMileStoneIdAttribute($input)
    {
        $this->attributes['mile_stone_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }
    
    public function recurring()
    {
        return $this->belongsTo(RecurringPeriod::class, 'recurring_id')->withTrashed();
    }
    
    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'project_id')->withTrashed();
    }
    
    public function mile_stone()
    {
        return $this->belongsTo(MileStone::class, 'milestone')->withTrashed();
    }
    
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function task_priority()
    {
        return $this->belongsTo(DynamicOption::class, 'priority')->withDefault()->withTrashed();
    }
    
    public function task_status()
    {
        return $this->belongsTo(DynamicOption::class, 'status')->withTrashed()->withDefault();
    }

    public function assigned_to()
    {
        return $this->belongsToMany(User::class, 'project_task_user');
    }

    public static function taskTotalTime( $task_id )
    {
        $result = DB::table('time_entries')->select( DB::raw( 'SUM(CASE
            WHEN end_date is NULL THEN ' . time() . '-UNIX_TIMESTAMP(start_date)
            ELSE UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)
            END) as total_logged_time' ) )
        ->where('task_id', '=', $task_id)
        ->whereNull('deleted_at')
        ->first();
        if ($result) {
            return $result->total_logged_time;
        }
        return 0;
    }

    public static function getTimeEntries( $project_id, $tasks_ids = [] )
    {
        
        if (count($tasks_ids) == 0) {
            $tasks     = ProjectTask::where('project_id', '=', $project_id)->get();
            $tasks_ids = [];
            foreach ($tasks as $task) {
                array_push($tasks_ids, $task->id );
            }
        }

        $date_set = getCurrentDateFormat();

        if (count($tasks_ids) > 0) {
            $time_entries = \App\TimeEntry::whereIn('task_id', $tasks_ids )->get();
   
            $i          = 0;
            foreach ($time_entries as $t) {                
                
                $time_entries[$i]['task_data']  = $t->task;
                $time_entries[$i]['completed_by_name'] = $t->completed_by->name;

                $start_date = Carbon::parse($t->start_date);
                $end_date = Carbon::parse($t->end_date);

                
                

                $now = Carbon::now();
                if (!is_null($t->end_date)) {
                    $time_entries[$i]['total_spent'] = $end_date->diffInSeconds( $start_date );
                } else {
                    $time_entries[$i]['total_spent'] = $now->diffInSeconds( $end_date );
                }
                $i++;
            }

            return $time_entries;
        }
        return [];
    }
}
