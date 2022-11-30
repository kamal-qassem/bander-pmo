<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class ClientProjectNote extends Model
{



    protected $fillable = ['project_id', 'description', 'user_id'];
    protected $hidden = [];
    public static $searchable = [ ];
    
    public static function boot()
    {
        parent::boot();

        ClientProject::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    
    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'project_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
    
}
