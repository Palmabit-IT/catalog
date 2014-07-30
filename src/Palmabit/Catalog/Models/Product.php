<?php namespace Palmabit\Catalog\Models;

/**
 * Class Product
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Jacopo\LaravelSingleTableInheritance\Models\Model;
use Palmabit\Catalog\ModelMultilanguage\Decorators\ProductLanguageDecorator;
use Palmabit\Catalog\ModelMultilanguage\Interfaces\EditableLanguageDescriptionInterface;
use Palmabit\Catalog\ModelMultilanguage\Traits\LanguageDescriptionsEditable;
use Palmabit\Catalog\Presenters\PresenterProducts;
use L;

class Product extends Model implements EditableLanguageDescriptionInterface
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

    protected $general_form_filter_enabled = false;

    protected $general_form_attributes = [
            "name",
            "slug",
            "long_description",
            "description",
            "slug_lang",
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

    public function presenter()
    {
        return new PresenterProducts($this);
    }

    /**
     * @return array
     */
    public function getGeneralFormAttributes()
    {
        return $this->general_form_attributes;
    }

    public function fill(array $attributes)
    {
        $attributes = $this->filterAttributesSettable($attributes);
        return parent::fill($attributes);
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function filterAttributesSettable(array $attributes)
    {
        $attributes = $this->filterAttributesByDefaultLanguage($attributes);
        return $attributes;
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function filterAttributesByDefaultLanguage(array $attributes)
    {
        if(! $this->general_form_filter_enabled) return $attributes;
        if(L::getDefault() == $this->getAttribute('lang')) return $attributes;

        $valid_keys = array_intersect(array_keys($attributes), $this->general_form_attributes);
        $attributes = array_only($attributes, $valid_keys);
        return $attributes;
    }

    /**
     * @param boolean $general_form_filter_enabled
     */
    public function setGeneralFormFilterEnabled($general_form_filter_enabled)
    {
        $this->general_form_filter_enabled = $general_form_filter_enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getGeneralFormFilterEnabled()
    {
        return $this->general_form_filter_enabled;
    }

    public function getUniqueData()
    {
        return array_diff($this->fillable, $this->general_form_attributes);
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