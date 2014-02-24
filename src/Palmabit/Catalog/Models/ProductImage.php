<?php namespace Palmabit\Catalog\Models;

class ProductImage extends Eloquent {

    protected $table = "product_image";

    protected $fillable = array("description,","product_id","featured","data", "description");

    public function product()
    {
        return $this->belongsTo("Product", "product_id");
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = $value;
    }

    public function getDataAttribute()
    {
        return base64_encode($this->attributes['data']);
    }

    public static function getImageFromUrl($url)
    {
        return file_get_contents($url);
    }
}
