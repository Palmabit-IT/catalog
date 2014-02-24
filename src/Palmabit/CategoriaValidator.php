<?php namespace Validators;

use Event;

class CategoriaValidator extends AbstractValidator
{
    protected static $rules = array(
        "descrizione" => "required|max:255",
        "slug" => ["required","AlphaDash"],
        "lang" => "max:2"
    );

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            if(isset($input["id"]))
            {
                static::$rules["slug"][] = "unique:categoria,slug,{$input['id']}";
            }
        });
    }
}