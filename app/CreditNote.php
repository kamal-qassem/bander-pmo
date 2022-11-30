<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

use App\Scopes\CreditNoteCustomerScope;
use DB;

/**
 * Class Invoice
 *
 * @package App
 * @property string $customer
 * @property string $currency
 * @property string $title
 * @property text $address
 * @property string $invoice_prefix
 * @property string $show_quantity_as
 * @property string $invoice_no
 * @property enum $status
 * @property string $reference
 * @property string $invoice_date
 * @property string $invoice_due_date
 * @property text $invoice_notes
 * @property string $tax
 * @property string $discount
 * @property decimal $amount
*/
class CreditNote extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['title', 'address', 'invoice_prefix', 'show_quantity_as', 'invoice_no', 'status', 'reference', 'invoice_date',  'invoice_notes', 'amount', 'customer_id', 'currency_id', 'tax_id', 'discount_id', 'products', 'slug', 'signature', 'delivery_address', 'show_delivery_address', 'admin_notes', 'terms_conditions', 'project_id','created_by_id', 'invoice_number_format', 'invoice_number_separator', 'invoice_number_length','credit_status','paymentstatus'];
    protected $hidden = [];
    public static $searchable = [ 'title', 'invoice_no', 'reference'
    ];
    
    public static function boot()
    {
        parent::boot();

        CreditNote::observe(new \App\Observers\UserActionsObserver);

         


        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
        if ( ! defined('CRON_JOB') ) {
            if ( isCustomer() ) {
                static::addGlobalScope(new CreditNoteCustomerScope);
            }
                
         }
        
       
    }

    public static $enum_status = ["Published" => "Published", "Draft" => "Draft"];
    public static $enum_credit_status = ["Open" => "Open", "Closed" => "Closed"];
    public static $enum_discounts_format = [
        "after_tax" => "Discount After Product Tax",
        "before_tax" => "Discount Before Product TAX",
    ];

    public static $enum_tax_format = [
        "after_tax" => "Tax After Product Tax", 
        "before_tax" => "Tax Before Product TAX",
    ];
    
    /**
     * Set to null if empty
     * @param $input
     */
    public function setCustomerIdAttribute($input)
    {
        $this->attributes['customer_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCurrencyIdAttribute($input)
    {
        $this->attributes['currency_id'] = $input ? $input : null;
    }
    
    /**
     * Set to null if empty
     * @param $input
     */
    public function setTaxIdAttribute($input)
    {
        $this->attributes['tax_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setDiscountIdAttribute($input)
    {
        $this->attributes['discount_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAmountAttribute($input)
    {
        $this->attributes['amount'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function getCurrencyAmountAttribute($input)
    {
        return digiCurrency( $this->attributes['amount'] );
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function getInvoiceNumberFullAttribute($input)
    {
        return $this->attributes['invoice_prefix'] . $this->attributes['invoice_no'];
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function getInvoiceNumberDisplayAttribute($input)
     {
        $invoice_number_format = ( $this->invoice_number_format ) ? $this->invoice_number_format : $this->attributes['invoice_number_format'];
        $invoice_number_separator = ( $this->invoice_number_separator ) ? $this->invoice_number_separator : $this->attributes['invoice_number_separator'];
        $invoice_number_length = ( $this->invoice_number_length ) ? $this->invoice_number_length : $this->attributes['invoice_number_length'];
        $invoice_no = ( $this->invoice_no ) ? $this->invoice_no : $this->attributes['invoice_no'];
        $invoice_prefix = ( $this->invoice_prefix ) ? $this->invoice_prefix : $this->attributes['invoice_prefix'];

        $invoice_date = ( $this->invoice_date ) ? $this->invoice_date : $this->attributes['invoice_date'];

        if ( empty( $invoice_date ) ) {
            $invoice_number_format = 'numberbased';
        }
        

        //$invoice_number_format = 'yearbased';
        $invoice_no_display = $invoice_no;
        if ( ! empty( $invoice_number_length ) ) {
            $invoice_no = str_pad($invoice_no, $invoice_number_length, 0, STR_PAD_LEFT);
        }
        if ( 'yearbased' === $invoice_number_format ) {
            $invoice_no_display = date('Y', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
        } elseif ( 'year2digits' === $invoice_number_format ) {
            $invoice_no_display = date('y', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
        } elseif ( 'yearmonthnumber' === $invoice_number_format ) {
            $invoice_no_display = date('Y', strtotime( $invoice_date ) ) . $invoice_number_separator . date('m', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
        } elseif ( 'yearbasedright' === $invoice_number_format ) {
            $invoice_no_display = $invoice_no . $invoice_number_separator . date('Y', strtotime( $invoice_date ) );
        } elseif ( 'year2digitsright' === $invoice_number_format ) {
            $invoice_no_display = $invoice_no . $invoice_number_separator . date('y', strtotime( $invoice_date ) );
        } elseif ( 'numbermonthyear' === $invoice_number_format ) {
            $invoice_no_display = $invoice_no . $invoice_number_separator . date('m', strtotime( $invoice_date ) ) . $invoice_number_separator . date('Y', strtotime( $invoice_date ) );
        }
        return $invoice_prefix . $invoice_no_display;
    }
    
    public function customer()
    {
        return $this->belongsTo(Contact::class, 'customer_id')->withDefault();
    }
	
	
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault()->withTrashed();
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withDefault()->withTrashed();
    }
    
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id')->withDefault()->withTrashed();
    }

    public function transactions()
    {
        return $this->hasMany(CreditNotePayment::class)->orderBy('id', 'DESC')->withTrashed();
    }

    public function history()
    {
        return $this->hasMany(CreditNoteHistory::class)->orderBy('id', 'DESC')->withTrashed();
    }

    public function created_by()
    {
        return $this->belongsTo(\App\User::class, 'created_by_id')->withDefault();
    }

    public function allowed_paymodes()
    {
        return $this->belongsToMany(\App\PaymentGateway::class, 'credit_note_paymentmodes')->withTrashed();
    }


    public function credit_note_products()
    {
        return $this->belongsToMany(CreditNote::class, 'credit_note_products');
    }

    public function credit_note_invoices()
    {
        return $this->belongsToMany(CreditNote::class, 'credit_note_credits', 'credit_note_id', 'invoice_id');
    }

    public function credit_history()
    {
        return $this->hasMany(CreditNoteCredit::class)->orderBy('created_at', 'DESC');
    }


    public function attached_products( $id )
    {
        return CreditNote::select(['pop.*'])
            ->join('credit_note_products as pop', 'pop.credit_note_id', '=', 'credit_notes.id')
            ->join('products', 'products.id', '=', 'pop.product_id')
            ->where('credit_notes.id', $id)->get();
    }

    public function total_refunds_by_credit_note( $id )
    {
        return \App\CreditNotePayment::where('credit_note_id', $id)->sum('amount');
    }

    public function total_credits_used_by_credit_note( $id )
    {
        return \App\CreditNoteCredit::where('credit_note_id', $id)->sum('amount');
    }

    public function update_credit_note( $id )
    {
        $total_refunds_by_credit_note = $this->total_refunds_by_credit_note($id);
        $total_credits_used           = $this->total_credits_used_by_credit_note($id);

        $status = '';
        $credit = CreditNote::find( $id );

        if ( $credit ) {
            // sum from table returns null if nothing found
            if ($total_credits_used || $total_refunds_by_credit_note) {
                $compare = $total_credits_used + $total_refunds_by_credit_note;
                
                
                if ( $credit ) {
                    if ( round($credit->amount, 2) == round( $compare, 2) ) {
                        $status = 'Closed';
                    }                    
                }
            }
            $credit->credit_status = $status;
            $credit->save();

            // Let us update Invoice Status.
            $applied_credits = DB::table('credit_note_credits')->where('credit_note_id', $id)->get();
            if ( $applied_credits->count() > 0 ) {
                foreach ( $applied_credits as $record ) {
                    $invoice = Invoice::find( $record->invoice_id );
                    if ( $invoice ) {
                        $invoice->update_invoice_status( $invoice->id );
                    }
                }
            }
        }
    }
}
