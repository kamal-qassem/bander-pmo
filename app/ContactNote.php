<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class ContactNote
 *
 * @package App
 * @property string $title
 * @property string $contact
 * @property text $notes
*/
class ContactNote extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $fillable = ['title', 'notes', 'contact_id'];
    protected $hidden = [];
    public static $searchable = [ 'title', 'notes'  ];
    
    public static function boot()
    {
        parent::boot();

        ContactNote::observe(new \App\Observers\UserActionsObserver);

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
