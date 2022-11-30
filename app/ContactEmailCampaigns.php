<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class ContactDocument
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $contact
*/
class ContactEmailCampaigns extends Model
{

    protected $fillable = ['list_id', 'list_name', 'subject', 'from_name', 'from_email', 'is_schedule', 'schedule_date', 'content', 'campaign_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ContactDocument::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setContactIdAttribute($input)
    {
        $this->attributes['contact_id'] = $input ? $input : null;
    }
    
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    
}
