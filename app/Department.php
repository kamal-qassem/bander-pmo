<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterByUser;

/**
 * Class Department
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $created_by
*/
class Department extends Model
{
    use SoftDeletes, FilterByUser;

    protected $fillable = ['name', 'description', 'created_by_id'];
    protected $hidden = [];
    public static $searchable = [ 'name'    ];
    
    public static function boot()
    {
        parent::boot();

        Department::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
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
    
}
