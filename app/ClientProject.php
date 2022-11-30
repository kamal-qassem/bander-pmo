<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use DB;
use App\Scopes\ClientProjectsScope;

/**
 * Class ClientProject
 *
 * @package App
 * @property string $title
 * @property string $client
 * @property enum $priority
 * @property double $budget
 * @property string $billing_type
 * @property string $phase
 * @property string $start_date
 * @property string $due_date
 * @property string $status
 * @property text $description
 * @property string $demo_url
*/
class ClientProject extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['title', 'priority', 'budget', 'phase', 'start_date', 'due_date', 'description', 'demo_url', 'client_id', 'billing_type_id', 'status_id', 'date_finished', 'progress', 'progress_from_tasks', 'project_rate_per_hour', 'estimated_hours', 'hourly_rate', 'currency_id'];
    protected $hidden = [];
    public static $searchable = [ 'title', 'description', 'start_date', 'due_date'];
    
    public static function boot()
    {
        parent::boot();

        ClientProject::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);

        if ( ! defined('CRON_JOB') ) {
            if ( isClient() ) {
                static::addGlobalScope(new ClientProjectsScope);
            }
        }
    }

    public static $enum_priority = ["Low" => "Low", "Medium" => "Medium", "High" => "High", "Urgent" => "Urgent"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setClientIdAttribute($input)
    {
        $this->attributes['client_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setBudgetAttribute($input)
    {
        if ($input != '') {
            $this->attributes['budget'] = $input;
        } else {
            $this->attributes['budget'] = null;
        }
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setBillingTypeIdAttribute($input)
    {
        $this->attributes['billing_type_id'] = $input ? $input : null;
    }


      /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getDueDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format('Y-m-d');
        } else {
            return '';
        }
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setStatusIdAttribute($input)
    {
        $this->attributes['status_id'] = $input ? $input : null;
    }
    
    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id')->withDefault();
    }
    
    public function billing_type()
    {
        return $this->belongsTo(ProjectBillingType::class, 'billing_type_id')->withDefault()->withTrashed();
    }
    
    public function assigned_to()
    {
        return $this->belongsToMany(User::class, 'client_project_user');
    }
    
    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id')->withDefault()->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault()->withTrashed();
    }

    public function payee()
    {
        return $this->belongsTo(Contact::class, 'payee_id')->withDefault();
    }

    public function project_tabs()
    {
        return $this->belongsToMany(ProjectTab::class, 'client_project_project_tab')->withTrashed();
    }


    public static function calculateProgress( $project_id ) {
        $project = self::find( $project_id );

        if ( $project->status === STATUS_COMPLETED ) {
            return 100;
        }

        if ( $project->progress_from_tasks == 'yes' ) {
            $total_project_tasks = \App\ProjectTask::where('project_id', $project_id)->count();
            $total_finished_tasks = \App\ProjectTask::where('project_id', $project_id)->where('status', STATUS_COMPLETED)->count();

            $percent = 0;
            if ($total_finished_tasks >= floatval($total_project_tasks)) {
                $percent = 100;
            } else {
                if ($total_project_tasks !== 0) {
                    $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
                }
            }
            return $percent;
        }

        return $project->progress;
    }

    public static function loggedBilling( $project_id ) {
        $project = self::find( $project_id );
        $data = [];
        if ($project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS) {
            $q = DB::table('time_entries')
            ->select(DB::raw('SUM(CASE
                    WHEN end_date is NULL THEN ' . time() . '-UNIX_TIMESTAMP(start_date)
                    ELSE UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)
                    END) as total_logged_time'))
            ->join('project_tasks AS pt', 'pt.id', '=', 'time_entries.task_id')
            ->where('pt.project_id', '=', $project_id)
            ->whereNull('pt.deleted_at')
            ->first();
            $seconds = $q->total_logged_time;

            $hourly_rate = $project->project_rate_per_hour;
            $hours       = seconds_to_time_format($seconds);
            $decimal     = secondsToQuantity($seconds);
            $total_money = 0;
            $total_money += ($decimal * $hourly_rate);

            $data['hours']       = $hours;
            $data['total_money'] = $total_money;
            $data['logged_time'] = $hours;
        } elseif ($project->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS) {
            $total_money    = 0;
            $_total_seconds = 0;
            $tasks        = $this->get_tasks($id, $conditions);

            $tasks = DB::table('project_tasks')->select(DB::raw('(SELECT SUM(CASE
            WHEN end_date is NULL THEN ' . time() . '-UNIX_TIMESTAMP(start_date)
            ELSE UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)
            END) FROM ' . env('DB_PREFIX') . 'time_entries WHERE task_id=' . env('DB_PREFIX') . 'project_tasks.id) as total_logged_time'))
            ->whereNull('deleted_at')
            ->get();
            foreach ($tasks as $task) {
                $seconds = $task->total_logged_time;
                $_total_seconds += $seconds;
                $total_money += secondsToQuantity($seconds) * $task->hourly_rate;
            }
            $data['total_seconds']  = $_total_seconds;
            $data['total_money']    = $total_money;
        }

        return $data;
    }

    public function data_billed_time($id)
    {
        return $this->_get_data_total_logged_time($id, [
            'billable' => 'yes',
            'billed'   => 'yes',
        ]);
    }

    private function _get_project_billing_data($id)
    {
        return self::find( $id );
    }

    public function total_logged_time($id)
    {
        $q = DB::table('time_entries')
            ->select(DB::raw('SUM(CASE
                    WHEN end_date is NULL THEN ' . time() . '-UNIX_TIMESTAMP(start_date)
                    ELSE UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)
                    END) as total_logged_time'))
            ->join('project_tasks AS pt', 'pt.id', '=', 'time_entries.task_id')
            ->where('pt.project_id', '=', $id)
            ->whereNull('pt.deleted_at');
        $q = $q->first();
        
        return $q->total_logged_time;
    }

    public function total_logged_time_by_billing_type( $id ) {
        $project_data = $this->_get_project_billing_data($id);
        
        $data         = [];
        if ( $project_data->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS ) {
            $seconds             = $this->total_logged_time($id);
            //dd( $seconds);
            $data                = $this->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            
            $data['logged_time'] = $data['hours'];
        } elseif ( $project_data->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS ) {
            $data = $this->_get_data_total_logged_time($id);
        }
        return $data;
    }

    public function data_billable_time( $id ) {
        
        return $this->_get_data_total_logged_time($id, [
            'billable' => 'yes',
        ]);
    }

    public function data_unbilled_time( $id ) {
        return $this->_get_data_total_logged_time($id, [
            'billable' => 'yes',
            'billed'   => 'no',
        ]);
    }

    private function _get_data_total_logged_time( $id, $conditions = [] ) {
        $project_data = self::find($id);
        $tasks        = $this->get_tasks($id, $conditions);

        if ($project_data->billing_type_id == 3) {
            $data                = $this->calculate_total_by_task_hourly_rate($tasks);
            $data['logged_time'] = seconds_to_time_format($data['total_seconds']);
        } elseif ($project_data->billing_type_id == 2) {
            $seconds = 0;
            foreach ($tasks as $task) {
                $seconds += $task->total_logged_time;
            }
            $data                = $this->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        }
        return $data;
    }
    
    private function get_tasks( $id, $conditions ) {
        $tasks = DB::table('project_tasks')->select('hourly_rate', DB::raw('(SELECT SUM(CASE
            WHEN end_date is NULL THEN ' . time() . '-UNIX_TIMESTAMP(start_date)
            ELSE UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)
            END) FROM ' . env('DB_PREFIX') . 'time_entries WHERE task_id=' . env('DB_PREFIX') . 'project_tasks.id) as total_logged_time'))
        ->where('project_id', '=', $id)
        ->whereNull('project_tasks.deleted_at')
        ;
        if ( isset( $conditions['billable'] ) ) {
            $tasks->where('billable', '=', $conditions['billable']);
        }
        if ( isset( $conditions['billed'] ) ) {
            $tasks->where('billed', '=', $conditions['billed']);
        }
        $tasks = $tasks->get();
        return $tasks;
    }

    function calculate_total_by_task_hourly_rate( $tasks ) {
        $total_money    = 0;
        $_total_seconds = 0;

        foreach ($tasks as $task) {
            $seconds = $task->total_logged_time;
            $_total_seconds += $seconds;
            $total_money += secondsToQuantity($seconds) * $task->hourly_rate;
        }

        return [
            'total_money'   => $total_money,
            'total_seconds' => $_total_seconds,
        ];
    }

    public function calculate_total_by_project_hourly_rate($seconds, $hourly_rate)
    {
        $hours       = seconds_to_time_format($seconds);
        $decimal     = secondsToQuantity($seconds);
        $total_money = 0;
        $total_money += ($decimal * $hourly_rate);

        return [
            'hours'       => $hours,
            'total_money' => $total_money,
        ];
    }

    public static function projectTickets( $project_id, $condition = '' ) {
        $query = \Kordy\Ticketit\Models\Ticket::where('project_id', '=', $project_id);
        if ( 'Open' === $condition ) {
            return $query->join('contacts', 'contacts.id', '=', 'ticketit.user_id')
            ->join('ticketit_statuses', 'ticketit_statuses.id', '=', 'ticketit.status_id')
            ->join('ticketit_priorities', 'ticketit_priorities.id', '=', 'ticketit.priority_id')
            ->join('ticketit_categories', 'ticketit_categories.id', '=', 'ticketit.category_id')
            ->where('status_id', '!=', SUPPORT_STATUS_COMPLETED);
        }
        if ( 'Closed' === $condition ) {
            
           return $query->join('contacts', 'contacts.id', '=', 'ticketit.user_id')
            ->join('ticketit_statuses', 'ticketit_statuses.id', '=', 'ticketit.status_id')
            ->join('ticketit_priorities', 'ticketit_priorities.id', '=', 'ticketit.priority_id')
            ->join('ticketit_categories', 'ticketit_categories.id', '=', 'ticketit.category_id')
            ->where('status_id',SUPPORT_STATUS_COMPLETED);
        }
        $query
        ->join('contacts', 'contacts.id', '=', 'ticketit.user_id')
        ->join('ticketit_statuses', 'ticketit_statuses.id', '=', 'ticketit.status_id')
        ->join('ticketit_priorities', 'ticketit_priorities.id', '=', 'ticketit.priority_id')
        ->join('ticketit_categories', 'ticketit_categories.id', '=', 'ticketit.category_id')
        ->select([
            'ticketit.id',
            'ticketit.created_at',
            'ticketit.subject AS subject',
            'ticketit_statuses.name AS status',
            'ticketit_statuses.color AS color_status',
            'ticketit_priorities.color AS color_priority',
            'ticketit_categories.color AS color_category',
            'ticketit.id AS agent',
            'ticketit.updated_at AS updated_at',
            'ticketit_priorities.name AS priority',
            'contacts.name AS owner',
            'ticketit.agent_id',
            'ticketit_categories.name AS category',
            'ticketit.project_id',
        ]);
        if ( 'Closed' === $condition ) {
            
        }
        return $query;
    }



    public static function supportTickets( $user_id, $type = 'own' ) {
        $query = \Kordy\Ticketit\Models\Ticket::query();
        if ( 'own' === $type ) {
            $query->where('user_id', '=', $user_id);
        }
        if ( 'agent' === $type ) {
            $query->where('agent_id', '=', $user_id);
        }
        $query
        ->join('contacts', 'contacts.id', '=', 'ticketit.user_id')
        ->join('ticketit_statuses', 'ticketit_statuses.id', '=', 'ticketit.status_id')
        ->join('ticketit_priorities', 'ticketit_priorities.id', '=', 'ticketit.priority_id')
        ->join('ticketit_categories', 'ticketit_categories.id', '=', 'ticketit.category_id')
        ->select([
            'ticketit.id',
            'ticketit.created_at',
            'ticketit.subject AS subject',
            'ticketit_statuses.name AS status',
            'ticketit_statuses.color AS color_status',
            'ticketit_priorities.color AS color_priority',
            'ticketit_categories.color AS color_category',
            'ticketit.id AS agent',
            'ticketit.updated_at AS updated_at',
            'ticketit_priorities.name AS priority',
            'contacts.name AS owner',
            'ticketit.agent_id',
            'ticketit_categories.name AS category',
            'ticketit.project_id',
        ]);
        return $query;
    }
}
