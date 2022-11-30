<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AssetsHistory
 *
 * @package App
 * @property string $asset
 * @property string $status
 * @property string $location
 * @property string $assigned_user
*/
class AssetsHistory extends Model
{
    protected $fillable = ['asset_id', 'status_id', 'location_id', 'assigned_user_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    

    /**
     * Set to null if empty
     * @param $input
     */
    public function setAssetIdAttribute($input)
    {
        $this->attributes['asset_id'] = $input ? $input : null;
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
    
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
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
