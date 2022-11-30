<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class ContactDocument
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $contact
*/
class ContactDocument extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $fillable = ['name', 'description', 'contact_id'];
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
