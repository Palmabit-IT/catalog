<?php namespace Palmabit\Catalog\Models;

use Eloquent;

class ProductImage extends Eloquent {

    protected $table = "product_image";

    protected $fillable = array("description","product_id","featured","data");

    public function product()
    {
        return $this->belongsTo('Palmabit\Catalog\Models\Product', "product_id");
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = $value;
    }

    public function getDataAttribute()
    {
        return isset($this->attributes['data']) ? base64_encode($this->attributes['data']) : null;    }

    public static function getImageFromUrl($url)
    {
        return file_get_contents($url);
    }
}
