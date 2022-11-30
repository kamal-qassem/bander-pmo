<?php
namespace Modules\Smstemplates\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Smstemplate
 *
 * @package App
 * @property string $title
 * @property string $key
 * @property text $content
*/
class Smstemplate extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'key', 'content'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Smstemplate::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }
    
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

     /**
     * [getRecordWithSlug description]
     * @param  [string] $slug [description]
     * @return [array]       [description]
     */
    public static function getRecordWithSlug($slug)
    {
        return Template::where('key','=',$slug)->first();
    }

    /**
     * Common email function to send emails
     * @param  [type] $template [key of the template]
     * @param  [type] $data     [data to be passed to view]
     * @return [type]           [description]
     */
    public function sendSms($template, $data)
    {        
        return \Modules\Sendsms\Entities\SendSm::sendSms( $data );        
    }    
}
