<?php
namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class ProductValidator  extends OverrideConnectionValidator{

    protected static $rules = [
        "code" => ["max:255"],
        "name" => "required|max:255",
        "slug" => ["required","max:255","AlphaDash"],
        "description" => "required|max:60",
        "description_long" => "max:8000",
        "lang" => "max:2",
        "video_link" => "max:255|url",
        "price1" => "required|currency",
        "price2" => "currency",
        "price3" => "required|currency",
        "price4" => "currency",
        'quantity_pricing_quantity' => 'integer|max:1000000'
    ];

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            /**
             * @param $input
             * @return bool
             */
            function isUpdate($input)
            {
                return isset($input["id"]);
            }

            if(isUpdate($input))
            {
                static::$rules["slug"][] = "unique:product,slug,{$input['id']}";
                static::$rules["code"][] = "unique:product,code,{$input['id']}";
            }
        });
    }
} 