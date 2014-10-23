<?php namespace Palmabit\Catalog\Models;

/**
 * Class Product
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\ModelMultilanguage\Decorators\ProductLanguageDecorator;
use Palmabit\Catalog\ModelMultilanguage\Interfaces\EditableLanguageDescriptionInterface;
use Palmabit\Catalog\ModelMultilanguage\Traits\LanguageDescriptionsEditable;
use Palmabit\Catalog\Presenters\PresenterProducts;
use L;
use Palmabit\Library\Models\SingleTableInheritance;

class Product extends SingleTableInheritance implements EditableLanguageDescriptionInterface
{
    use LanguageDescriptionsEditable;

    protected $language_descriptions = [];

    protected $description_attributes = [
            "name",
            "description",
            "long_description",
            "lang",
            "slug",
            "product_id"
    ];

    protected $table = "product";

    protected $fillable = [
        "id",
        "code",
        "featured",
        "order",
        "public",
        "offer",
        "stock",
        "with_vat",
        "video_link",
        "professional",
        "price1",
        "price2",
        "price3",
        "price4",
        'quantity_pricing_enabled',
        'quantity_pricing_quantity',
        'quantity_pricing_quantity_non_professional'
    ];

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
    protected static $my_attributes = [
        "id",
        "blocked",
        "code",
        "name",
        "slug",
        "long_description",
        "language_descriptions",
        "image",
        "description",
        "featured",
        "lang",
        "pivot",
        "order",
        "category",
        "public",
        "offer",
        "stock",
        "with_vat",
        "video_link",
        "professional",
        "price1",
        "price2",
        "price3",
        "price4",
        'quantity_pricing_enabled',
        'quantity_pricing_quantity',
        'price_small',
        'price_big',
        'categories',
        'quantity_pricing_quantity_non_professional',
        'quantity_pricing_quantity_used',
        'flag',
        'availableflags',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    public function categories()
    {
        return $this->belongsToMany('Palmabit\Catalog\Models\Category', "product_category", "product_id", "category_id");
    }

    public function product_images()
    {
        return $this->hasMany('Palmabit\Catalog\Models\ProductImage', "product_id");
    }

    public function accessories()
    {
        return $this->belongsToMany('Palmabit\Catalog\Models\Product', "products_products", "first_product_id", "second_product_id");
    }

    public function descriptions()
    {
        return $this->hasMany('Palmabit\Catalog\Models\ProductDescription','product_id');
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

    public function presenter($current_lang = null)
    {
        return new PresenterProducts($this, $current_lang);
    }

    public function decorateLanguage($current_lang = null)
    {
        return new ProductLanguageDecorator($this, $current_lang);
    }

    public function delete()
    {
        foreach($this->descriptions()->get() as $description)
        {
            $description->delete();
        }

        return parent::delete();
    }

}