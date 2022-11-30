<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

/**
 * Class Task
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $status
 * @property string $attachment
 * @property string $due_date
 * @property string $user
*/
class Task extends Model
{
    protected $fillable = ['name', 'description', 'attachment', 'due_date', 'status_id', 'user_id', 'start_date'];
    protected $hidden = [];
    public static $searchable = [ 'name', 'description'
    ];
    
    public static function boot()
    {
        parent::boot();

        Task::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
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
    public function setUserIdAttribute($input)
    {
        $this->attributes['user_id'] = $input ? $input : null;
    }
    
    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }
    
    public function tag()
    {
        return $this->belongsToMany(TaskTag::class, 'task_task_tag');
    }
    
  
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
    
}
