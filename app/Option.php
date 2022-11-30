<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Brand
 *
 * @package App
 * @property string $title
 * @property string $icon
 * @property enum $status
*/
class Option extends Model
{

    protected $fillable = ['name', 'value', 'auto_load'];
    protected $hidden = [];
    public static $searchable = [];
    public $timestamps = false;
    
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
}
