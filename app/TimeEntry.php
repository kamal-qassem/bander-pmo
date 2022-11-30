<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TimeEntry
 *
 * @package App
 * @property string $project
 * @property string $start_date
 * @property string $end_date
 * @property text $description
*/
class TimeEntry extends Model
{
    use SoftDeletes;

    protected $fillable = ['start_date', 'end_date', 'description', 'project_id', 'task_id', 'completed_by_id', 'user_id', 'hourly_rate'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        TimeEntry::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setProjectIdAttribute($input)
    {
        $this->attributes['project_id'] = $input ? $input : null;
    }
    
    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'project_id')->withTrashed();
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'task_id')->withDefault();
    }

    public function completed_by()
    {
        return $this->belongsTo(User::class, 'completed_by_id')->withDefault();
    }
    
}
