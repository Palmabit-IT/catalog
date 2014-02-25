<?php
namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\AbstractValidator;

class ProductValidator  extends AbstractValidator{

    protected static $rules = [
        "code" => ["max:255"],
        "name" => "required|max:255",
        "slug" => ["required","max:255","AlphaDash"],
        "description" => "max:255",
        "description_long" => "max:255",
        "lang" => "max:2",
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