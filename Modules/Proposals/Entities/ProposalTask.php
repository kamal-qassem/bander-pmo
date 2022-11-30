<?php
namespace Modules\Proposals\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterByUser;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class ProposalTask
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
 * @property string $invoice
 * @property decimal $hourly_rate
 * @property integer $milestone
 * @property integer $kanban_order
 * @property integer $milestone_order
 * @property enum $visible_to_client
 * @property enum $deadline_notified
 * @property string $created_by
 * @property string $mile_stone
*/
class ProposalTask extends Model implements HasMedia
{
    use SoftDeletes, FilterByUser, HasMediaTrait;

    protected $fillable = ['name', 'description', 'priority_id', 'startdate', 'duedate', 'datefinished', 'status_id', 'recurring_type', 'recurring_value', 'cycles', 'total_cycles', 'last_recurring_date', 'is_public', 'billable', 'billed', 'hourly_rate', 'kanban_order', 'milestone_order', 'visible_to_client', 'deadline_notified', 'recurring_id', 'proposal_id', 'created_by_id', 'mile_stone_id'];
    protected $hidden = [];
    public static $searchable = [
        'name',
    ];
    
    public static function boot()
    {
        parent::boot();

        ProposalTask::observe(new \App\Observers\UserActionsObserver);
    }

    public static $enum_recurring_type = ["" => "Please select", "day" => "Day", "week" => "Week", "month" => "Month", "year" => "Year"];

    public static $enum_is_public = ["yes" => "Yes", "no" => "No"];

    public static $enum_billable = ["yes" => "Yes", "no" => "No"];

    public static $enum_billed = ["yes" => "Yes", "no" => "No"];

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
    public function setInvoiceIdAttribute($input)
    {
        $this->attributes['invoice_id'] = $input ? $input : null;
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
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setMileStoneIdAttribute($input)
    {
        $this->attributes['mile_stone_id'] = $input ? $input : null;
    }
    
    public function recurring()
    {
        return $this->belongsTo(\Modules\RecurringPeriods\Entities\RecurringPeriod::class, 'recurring_id')->withTrashed();
    }
    
    public function invoice()
    {
        return $this->belongsTo(\Modules\Proposals\Entities\Proposal::class, 'proposal_id')->withTrashed();
    }
    
    public function created_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by_id');
    }
    
    public function mile_stone()
    {
        return $this->belongsTo(\App\MileStone::class, 'mile_stone_id')->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo(\Modules\DynamicOptions\Entities\DynamicOption::class, 'status_id')->withDefault()->withTrashed();
    }

    public function priority()
    {
        return $this->belongsTo(\Modules\DynamicOptions\Entities\DynamicOption::class, 'priority_id')->withTrashed();
    }

    public function assigned_to()
    {
        return $this->belongsToMany(\App\User::class, 'proposal_task_user');
    }
    
}
