<?php
namespace Modules\InvoiceAdditional\Entities;

use Modules\DynamicOptions\Entities\DynamicOption;
use Modules\RecurringPeriods\Entities\RecurringPeriod;
use App\Invoice;
use App\User;
use App\MileStone;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class InvoiceTask
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $priority
 * @property string $startdate
 * @property string $duedate
 * @property string $datefinished
 * @property string $status
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
 * @property integer $kanban_order
 * @property integer $milestone_order
 * @property enum $visible_to_client
 * @property enum $deadline_notified
 * @property string $created_by
 * @property string $mile_stone
*/
class InvoiceTask extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $fillable = ['name', 'description', 'startdate', 'duedate', 'datefinished', 'recurring_type', 'recurring_value', 'cycles', 'total_cycles', 'last_recurring_date', 'is_public', 'billable', 'billed', 'hourly_rate', 'kanban_order', 'milestone_order', 'visible_to_client', 'deadline_notified', 'priority_id', 'status_id', 'recurring_id', 'invoice_id', 'created_by_id', 'mile_stone_id'];
    protected $hidden = [];
    public static $searchable = [
        'name',
    ];
    
    public static function boot()
    {
        parent::boot();

        InvoiceTask::observe(new \App\Observers\UserActionsObserver);
    }

    public static $enum_recurring_type = ["" => "Please select", "day" => "Day", "week" => "Week", "month" => "Month", "year" => "Year"];

    public static $enum_is_public = ["yes" => "Yes", "no" => "No"];

    public static $enum_billable = ["yes" => "Yes", "no" => "No"];

    public static $enum_billed = ["yes" => "Yes", "no" => "No"];

    public static $enum_visible_to_client = ["yes" => "Yes", "no" => "No"];

    public static $enum_deadline_notified = ["yes" => "Yes", "no" => "No"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setPriorityIdAttribute($input)
    {
        $this->attributes['priority_id'] = $input ? $input : null;
    }

  

    /**
     * Set to null if empty
     * @param $input
     */
    public function setStatusIdAttribute($input)
    {
        $this->attributes['status_id'] = $input ? $input : null;
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
            $this->attributes['last_recurring_date'] = Carbon::parse($input)->format('Y-m-d');
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
            return Carbon::parse($input)->format('Y-m-d');
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
    
    public function priority()
    {
        return $this->belongsTo(\Modules\DynamicOptions\Entities\DynamicOption::class, 'priority_id')->withTrashed();
    }
    
    public function status()
    {
        return $this->belongsTo(\Modules\DynamicOptions\Entities\DynamicOption::class, 'status_id')->withDefault()->withTrashed();
    }
    
    public function recurring()
    {
        return $this->belongsTo(RecurringPeriod::class, 'recurring_id')->withTrashed();
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id')->withTrashed();
    }
    
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    
    public function mile_stone()
    {
        return $this->belongsTo(MileStone::class, 'mile_stone_id')->withTrashed();
    }

    public function assigned_to()
    {
        return $this->belongsToMany(\App\User::class, 'invoice_task_user');
    }
    
}
