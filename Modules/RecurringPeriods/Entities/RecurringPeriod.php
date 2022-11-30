<?php

namespace Modules\RecurringPeriods\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringPeriod extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'value', 'type', 'description'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        RecurringPeriod::observe(new \App\Observers\UserActionsObserver);
    }
}
