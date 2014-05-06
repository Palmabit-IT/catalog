<?php namespace Palmabit\Catalog\Models;
/**
 * Class Category
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Baum\Node as Model;

class Category extends Model{

    protected $table = "category";

    protected $fillable = array("id", "description","slug","image", "slug_lang", "lang", "name", "parent_id");

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

}