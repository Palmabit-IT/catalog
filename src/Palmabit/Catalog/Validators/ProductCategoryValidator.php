<?php namespace Palmabit\Catalog\Validators;

use Event, Input, DB;
use Illuminate\Support\MessageBag;
use Palmabit\Library\Exceptions\ValidationException;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class ProductCategoryValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
    );

    public function __construct()
    {
        Event::listen('repository.products.attachCategory', function($product_id, $category_id){
            $occurrence  = DB::table('product_category')->where('product_id', '=', $product_id)->where('category_id', '=', $category_id)->count();

            if($occurrence != 0)
            {
                $this->errors = new MessageBag(["category_id" => "La categoria è già stata associata"]);
                throw new ValidationException;
            }
        });
    }
}