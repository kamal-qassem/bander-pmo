<?php

namespace Modules\DynamicOptions\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicOption extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'module', 'type', 'description', 'color'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        DynamicOption::observe(new \App\Observers\UserActionsObserver);
    }
}
