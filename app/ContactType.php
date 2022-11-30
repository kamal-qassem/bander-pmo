<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ContactType
 *
 * @package App
 * @property string $name
 * @property text $description
*/
class ContactType extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'name', 'color', 'type', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    protected $table = 'roles';
    
    public static function boot()
    {
        parent::boot();

        ContactType::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
