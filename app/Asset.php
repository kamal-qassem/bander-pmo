<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class Asset
 *
 * @package App
 * @property string $category
 * @property string $serial_number
 * @property string $title
 * @property string $photo1
 * @property string $status
 * @property string $location
 * @property string $assigned_user
 * @property text $notes
*/
class Asset extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = ['serial_number', 'title', 'photo1', 'notes', 'category_id', 'status_id', 'location_id', 'assigned_user_id'];
    protected $hidden = [];
    public static $searchable = [ 'title', 'serial_number' ];
    
    public static function boot()
    {
        parent::boot();

        Asset::observe(new \App\Observers\UserActionsObserver);

        Asset::observe(new \App\Observers\AssetsHistoryObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCategoryIdAttribute($input)
    {
        $this->attributes['category_id'] = $input ? $input : null;
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
    public function setLocationIdAttribute($input)
    {
        $this->attributes['location_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setAssignedUserIdAttribute($input)
    {
        $this->attributes['assigned_user_id'] = $input ? $input : null;
    }
    
    public function category()
    {
        return $this->belongsTo(AssetsCategory::class, 'category_id');
    }
    
    public function status()
    {
        return $this->belongsTo(AssetsStatus::class, 'status_id');
    }
    
    public function location()
    {
        return $this->belongsTo(AssetsLocation::class, 'location_id');
    }
    
    public function assigned_user()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    
}
