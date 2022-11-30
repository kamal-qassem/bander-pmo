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
class ProjectDiscussion extends Model
{

    use SoftDeletes;

    protected $fillable = ['subject', 'description', 'show_to_customer', 'last_activity', 'created_by', 'contact_id', 'project_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ProjectDiscussion::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public static $show_to_customer = ["yes" => "Yes", "no" => "No"];

    

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
        return $this->belongsTo(ClientProject::class, 'project_id')->withDefault()->withTrashed();
    }

    public function created_name()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault()->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id')->withDefault()->withTrashed();
    }
    
}
