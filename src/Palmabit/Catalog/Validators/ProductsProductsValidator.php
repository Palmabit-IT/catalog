<?php namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\OverrideConnectionValidator;
use Palmabit\Catalog\Validators\Traits\eloquentManyToManyUniqueValidatorTrait;

class ProductsProductsValidator extends OverrideConnectionValidator
{
    use eloquentManyToManyUniqueValidatorTrait;

    protected static $rules = [];

    public function __construct()
    {
        Event::listen('repository.products.attachProduct', function($first_product_id, $second_product_id)
        {
            $this->validateExistence($first_product_id, $second_product_id, 'products_products','first_product_id', 'second_product_id');
        });
    }
}
