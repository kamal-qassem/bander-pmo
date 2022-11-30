<?php
namespace Modules\InvoiceAdditional\Entities;

use App\Invoice;
use App\User;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InvoiceNote
 *
 * @package App
 * @property text $description
 * @property string $date_contacted
 * @property string $quote
 * @property string $created_by
*/
class InvoiceNote extends Model
{
    use SoftDeletes;

    protected $fillable = ['description', 'date_contacted', 'invoice_id', 'created_by_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        InvoiceNote::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setDateContactedAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['date_contacted'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['date_contacted'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getDateContactedAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setQuoteIdAttribute($input)
    {
        $this->attributes['quote_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }
    
    public function invoice()
    {
        return $this->belongsTo(\App\Invoice::class, 'invoice_id')->withTrashed();
    }

  
    public function created_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by_id');
    }

    
    
}
