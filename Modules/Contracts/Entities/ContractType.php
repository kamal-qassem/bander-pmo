<?php
namespace Modules\Contracts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class ProductCategory
 *
 * @package App
 * @property string $name
 * @property text $description
 * @property string $photo
*/
class ContractType extends Model
{
    protected $fillable = ['name', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

      ContractType::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
