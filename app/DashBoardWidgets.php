<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


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
class DashBoardWidgets extends Model
{
    
    protected $fillable = ['title', 'status', 'order', 'type', 'slug', 'columns'];
    protected $hidden = [];
    public static $searchable = [];
    protected $table = 'dashboard_widgets_role';
    
    public static function boot()
    {
        parent::boot();

        
    }
    
}
