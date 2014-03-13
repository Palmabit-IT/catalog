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
        "public_price" => "required|currency",
        "logged_price" => "required|currency",
        "professional_price" => "required|currency",
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