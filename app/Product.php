<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

/**
 * Class Product
 *
 * @package App
 * @property string $name
 * @property string $product_code
 * @property decimal $actual_price
 * @property decimal $sale_price
 * @property string $ware_house
 * @property text $description
 * @property integer $stock_quantity
 * @property integer $alert_quantity
 * @property string $thumbnail
 * @property string $hsn_sac_code
 * @property string $product_size
 * @property string $product_weight
 * @property string $brand
*/
class Product extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['name', 'product_code', 'actual_price', 'sale_price', 'description', 'stock_quantity', 'alert_quantity', 'thumbnail', 'hsn_sac_code', 'product_size', 'product_weight', 'ware_house_id', 'brand_id', 'tax_id', 'discount_id', 'measurement_unit', 'product_status', 'excerpt', 'prices', 'prices_available'];
    protected $hidden = [];
    public static $searchable = [ 'name', 'product_code', 'description'    ];
    
    public static $enum_product_status = ["Active" => "Active", "Inactive" => "Inactive", 'Damaged' => 'Damaged'];

    public static function boot()
    {
        parent::boot();
        // ob_start();
        Product::observe(new \App\Observers\UserActionsObserver);

        static::addGlobalScope(new \App\Scopes\DefaultOrderScope);
    }
    /**
     * Set attribute to money format
     * @param $input
     */
    public function setActualPriceAttribute($input)
    {
        $this->attributes['actual_price'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setSalePriceAttribute($input)
    {
        $this->attributes['sale_price'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setWareHouseIdAttribute($input)
    {
        $this->attributes['ware_house_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setStockQuantityAttribute($input)
    {
        $this->attributes['stock_quantity'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAlertQuantityAttribute($input)
    {
        $this->attributes['alert_quantity'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setBrandIdAttribute($input)
    {
        $this->attributes['brand_id'] = $input ? $input : null;
    }
    
    public function category()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }
    
    public function product_currency()
    {
        return $this->belongsToMany(Currency::class, 'product_currency');
    }
    
    public function ware_house()
    {
        return $this->belongsTo(Warehouse::class, 'ware_house_id')->withTrashed();
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id')->withTrashed();
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id')->withTrashed();
    }

    public function measurement()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit')->withTrashed();
    }
    
}
