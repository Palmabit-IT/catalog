<?php namespace Palmabit\Catalog\Models;
/**
 * Class Category
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Baum\Node as Model;
use L;

class Category extends Model{

    protected $table = "category";

    protected $fillable = array("image","name", "parent_id","order","depth", "parent_id");

    public function products()
    {
        return $this->belongsToMany('Palmabit\Catalog\Models\Product',"product_category", "category_id", "product_id");
    }

    public function setImageAttribute($value)
    {
        $this->attributes['image'] = $value;
    }

    public function getImageAttribute()
    {
        return isset($this->attributes['image']) ? base64_encode($this->attributes['image']) : null;
    }

    public function getRawImage()
    {
        return isset($this->attributes['image']) ? $this->attributes['image'] : null;
    }
    
    /**
     * @override
     */
    public function children() {
        return $this->hasMany(get_class($this), $this->getParentColumnName());
    }

    public function category_description()
    {
        return $this->hasMany('Palmabit\Catalog\Models\CategoryDescription');
    }

    public function getDescriptionAttribute()
    {
        return $this->category_description()->whereLang(L::get())->pluck('description');
    }

    public function getSlugLangAttribute()
    {
        return $this->category_description()->whereLang(L::get())->pluck('slug');
    }

    public function getSlugAttribute()
    {
        return $this->category_description()->whereLang(L::get())->pluck('slug');
    }
}