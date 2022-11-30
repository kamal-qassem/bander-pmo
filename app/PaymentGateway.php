<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentGateway
 *
 * @package App
 * @property string $name
 * @property string $description
 * @property string $logo
*/
class PaymentGateway extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'logo'];
    protected $hidden = [];
    public static $searchable = [
        'name',
    ];
    
    public static function boot()
    {
        parent::boot();

        PaymentGateway::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
