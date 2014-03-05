<?php
namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class ProductValidator  extends OverrideConnectionValidator{

    protected static $rules = [
        "code" => ["max:255"],
        "name" => "required|max:255",
        "slug" => ["required","max:255","AlphaDash"],
        "description" => "max:8000",
        "description_long" => "max:8000",
        "lang" => "max:2",
        "stock" => "integer",
        "video_link" => "max:255|url",
        "public_price" => "currency",
        "logged_price" => "currency",
        "professional_price" => "currency",
    ];

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            if(isset($input["id"]))
            {
                static::$rules["slug"][] = "unique:product,slug,{$input['id']}";
            }
        });
    }
} 