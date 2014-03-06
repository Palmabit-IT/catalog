<?php namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class CategoryValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "description" => "required|max:255",
        "slug" => ["required","AlphaDash"],
        "lang" => "max:2",
    );

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            if(isset($input["id"]))
            {
                static::$rules["slug"][] = "unique:category,slug,{$input['id']}";
            }
        });
    }
}
