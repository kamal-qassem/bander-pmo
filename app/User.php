<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;
use Hash;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\DefaultUserScope;
use Illuminate\Validation\Rule;
/**
 * Class User
 *
 * @package App
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
*/
class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use SoftDeletes;

    protected $table = 'contacts';
    
    protected $fillable = ['name', 'email', 'password', 'remember_token', 'department_id', 'status', 'theme', 'portal_language', 'confirmation_code', 'ticketit_admin', 'ticketit_agent', 'hourly_rate', 'color_theme', 'color_skin'];
    protected $hidden = ['password', 'remember_token'];

    
    public static function updateValidation($request)
    {
        return [
            'role' => 'array|required',
            'role.*' => 'integer|exists:roles,id|max:4294967295|required',
        ];
    }

    public static function boot()
    {
        parent::boot();

        User::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);

        static::addGlobalScope(new \App\Scopes\DefaultUserScope);
    }
    
    
    /**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }
    
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }


    /**
     * Set to null if empty
     * @param $input
     */
    public function setDepartmentIdAttribute($input)
    {
        $this->attributes['department_id'] = $input ? $input : null;
    }

    public function contact_type()
    {
        return $this->belongsToMany(ContactType::class, 'contact_contact_type', 'contact_id')->withTrashed();
    }
        
    public static function roleadmins()
    {
        return User::whereHas("contact_type",
                   function ($query) {
                       $query->where('slug', ROLE_ADMIN);
                   });
    }
    
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withTrashed();
    }
    
    public function topics() {
        return $this->hasMany(MessengerTopic::class, 'receiver_id')->orWhere('sender_id', $this->id);
    }

    public function inbox()
    {
        return $this->hasMany(MessengerTopic::class, 'receiver_id');
    }

    public function outbox()
    {
        return $this->hasMany(MessengerTopic::class, 'sender_id');
    }
    public function internalNotifications()
    {
        return $this->belongsToMany(InternalNotification::class)
            ->withPivot('read_at')
            ->orderBy('internal_notification_user.created_at', 'desc')
            ->limit(10);
    }
    
    public function sendPasswordResetNotification($token)
    {
       $this->notify(new ResetPassword($token));
    }
}
