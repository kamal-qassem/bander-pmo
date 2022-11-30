<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactCompany
 *
 * @package App
 * @property string $name
 * @property string $email
 * @property string $address
 * @property string $website
*/
class ContactCompany extends Model
{
    protected $fillable = ['name', 'email', 'address', 'website', 'country_id'];
    protected $hidden = [];
    public static $searchable = [ 'name', 'email', 'address' ];
    
    public static function boot()
    {
        parent::boot();

        ContactCompany::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id')->withDefault()->withTrashed();
    }
    
}
