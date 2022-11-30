<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;


/**
 * Class Expense
 *
 * @package App
 * @property string $account
 * @property string $expense_category
 * @property string $entry_date
 * @property string $amount
 * @property text $description
 * @property string $payee
 * @property string $payment_method
 * @property string $ref_no
*/
class DashBoard extends Model
{
    
    protected $fillable = ['title', 'status', 'type', 'slug', 'role_id', 'columns'];
    protected $hidden = [];
    public static $searchable = [];
    protected $table = 'dashboard_widgets';
    
    public static function boot()
    {
        parent::boot();
        
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setTitleAttribute($input)
    {
        $this->attributes['title'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setStatusAttribute($input)
    {
        $this->attributes['status'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
  

    
    /**
     * Set to null if empty
     * @param $input
     */
    public function setOrderAttribute($input)
    {
        $this->attributes['order'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setRoleIdAttribute($input)
    {
        $this->attributes['role_id'] = $input ? $input : null;
    }
    
    public function role()
    {
        return $this->belongsTo(\App\Role::class, 'role_id')->withTrashed();
    }

    public function role_widgets()
    {
        return $this->belongsToMany(\App\Role::class, 'dashboard_widgets_role', 'dash_board_id', 'role_id');
    }

    public function get_widget_field( $role_id, $widget_id, $field = 'display_order' )
    {
        $order = DB::table('dashboard_widgets_role')->where('role_id', $role_id)->where('dash_board_id', $widget_id)->first();
        if ( $order ) {
            $order = $order->$field;
        } else {
            $order = '';
        }
        return $order;
    }
    
}
