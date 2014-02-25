<?php namespace Validators;

use Event;
use Palmabit\Library\Validators\AbstractValidator;

class CategoryValidator extends AbstractValidator
{
    protected static $rules = array(
        "description" => "required|max:255",
        "slug" => ["required","AlphaDash"],
        "lang" => "max:2"
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