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

    protected $fillable = array("code","name","slug","long_description","description","featured","lang", "slug_land", "order");

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
    protected static $my_attributes = array("id","code","name","slug","long_description","description","featured","lang","pivot","slug_lang", "order", "category");

    public function categories()
    {
        return $this->belongsToMany('Category',"product_category", "product_id", "category_id");
    }

    public function product_images()
    {
        return $this->hasMany("ProductImage", "product_id");
    }
} 