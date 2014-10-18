<?php  namespace Palmabit\Catalog\Models; 

use Illuminate\Database\Eloquent\Model;

class CategoryDescription extends Model{

    protected $table = "category_description";

    protected $fillable = array("description","slug","slug_lang","lang", "category_id");

    public function category()
    {
        return $this->belongsTo('Palmabit\Catalog\Models\Category','category_id');
    }

    public function getNameAttribute()
    {
        return $this->category()->pluck('name');
    }

    public function getImageAttribute()
    {
        return $this->category()->pluck('image');
    }

    public function setNameAttribute($value)
    {
        return $this->category()->update(['name' => $value]);
    }

    public function setImageAttribute($value)
    {
        return $this->category()->update(['image' => $value]);
    }
} 