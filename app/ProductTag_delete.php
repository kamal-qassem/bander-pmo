<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductTag
 *
 * @package App
 * @property string $name
*/
class ProductTag extends Model
{
    //use SoftDeletes;

    
    protected $fillable = ['name'];
    

    public static function storeValidation($request)
    {
        return [
            'name' => 'max:191|required|unique:product_tags,name'
        ];
    }

    public static function updateValidation($request)
    {
        return [
            'name' => 'max:191|required|unique:product_tags,name,'.$request->route('product_tag')
        ];
    }

    

    
    
    
}
