<?php
namespace Modules\Contracts\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ContractsNote
 *
 * @package App
 * @property text $description
 * @property string $date_contacted
 * @property string $contract
*/
class ContractsNote extends Model
{
    use SoftDeletes;

    protected $fillable = ['description', 'date_contacted', 'contract_id', 'created_by_id'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        ContractsNote::observe(new \App\Observers\UserActionsObserver);
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
        $this->attributes['contract_id'] = $input ? $input : null;
    }
    
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id')->withTrashed();
    }

    public function created_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by_id');
    }
    
}
