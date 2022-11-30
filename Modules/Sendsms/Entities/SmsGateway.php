<?php
namespace Modules\Sendsms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SmsGateway
 *
 * @package App
 * @property string $name
 * @property string $key
 * @property text $description
*/
class SmsGateway extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'key', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        SmsGateway::observe(new \App\Observers\UserActionsObserver);
    }
    
}
