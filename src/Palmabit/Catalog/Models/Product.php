<?php namespace Palmabit\Catalog\Models;
/**
 * Class Product
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Jacopo\LaravelSingleTableInheritance\Models\Model;

class Product extends Model
{
    protected $table = "product";

    protected $fillable = array("id","code","name","slug","long_description","description","featured","lang", "slug_lang", "order", "public", "offer", "stock", "with_vat", "video_link", "professional", "price1", "price2", "price3", "price4", 'quantity_pricing_enabled', 'quantity_pricing_quantity','quantity_pricing_quantity_non_professional');

    protected $table_type = 'General';
    /**
     * The name of the colum that holds table_type
     * @var string
     */
    protected static $table_type_field = 'type';
    /**
     * The list of attributes that belongs to the class
     * @var array
     */
    protected static $my_attributes = array("id","code","name","slug","long_description","image","description","featured","lang","pivot","slug_lang", "order", "category", "public", "offer", "stock", "with_vat", "video_link", "professional", "price1", "price2", "price3", "price4" , 'quantity_pricing_enabled', 'quantity_pricing_quantity', 'price_small', 'price_big','categories','quantity_pricing_quantity_non_professional','quantity_pricing_quantity_used');

    public function categories()
    {
        return $this->belongsToMany('Palmabit\Catalog\Models\Category',"product_category", "product_id", "category_id");
    }

    public function product_images()
    {
        return $this->hasMany('Palmabit\Catalog\Models\ProductImage', "product_id");
    }

    public function accessories()
    {
        return $this->belongsToMany('Palmabit\Catalog\Models\Product', "products_products", "first_product_id", "second_product_id");
    }

    public function setPrice1Attribute($value)
    {
        $this->attributes['price1'] = $value ? $value : null;
    }

    public function setPrice2Attribute($value)
    {
        $this->attributes['price2'] = $value ? $value : null;
    }

    public function setPrice3Attribute($value)
    {
        $this->attributes['price3'] = $value ? $value : null;
    }

    public function setPrice4Attribute($value)
    {
        $this->attributes['price4'] = $value ? $value : null;
    }

    public function isAvailabile()
    {
        return (boolean)$this->attributes['stock'] ? true : false;
    }
    
}