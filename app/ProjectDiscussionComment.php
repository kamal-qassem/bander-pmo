<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;


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
class ProjectDiscussionComment extends Model implements HasMedia
{

    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['description', 'created_by_id', 'contact_id', 'project_id', 'discussion_id', 'parent_id', 'attachment'];
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
        return $this->belongsTo(User::class, 'created_by_id')->withDefault()->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id')->withDefault()->withTrashed();
    }

    public function parent_comment()
    {
        return $this->belongsTo(ProjectDiscussionComment::class, 'parent_id')->withDefault()->withTrashed();
    }
    
}
