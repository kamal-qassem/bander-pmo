<?php
namespace Modules\DatabaseBackup\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DatabaseBackup
 *
 * @package App
 * @property string $name
 * @property string $storage_location
*/
class DatabaseBackup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'storage_location'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        DatabaseBackup::observe(new \App\Observers\UserActionsObserver);
    }
    
}
