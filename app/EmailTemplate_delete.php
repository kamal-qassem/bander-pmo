<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmailTemplate
 *
 * @package App
 * @property string $name
 * @property string $subject
 * @property text $body
*/
class EmailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'key', 'subject', 'body'];
    protected $hidden = [];
    public static $searchable = [
        'name',
    ];
    
    public static function boot()
    {
        parent::boot();

        EmailTemplate::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
