<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AssetsStatus
 *
 * @package App
 * @property string $title
*/
class AssetsStatus extends Model
{
    protected $fillable = ['title'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        AssetsStatus::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
