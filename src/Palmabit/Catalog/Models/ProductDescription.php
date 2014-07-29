<?php  namespace Palmabit\Catalog\Models; 

use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model{

    protected $table = 'product_description';

    protected $fillable = [
        "name",
        "description",
        "long_description",
        "lang",
        "slug",
        "product_id"
    ];

} 