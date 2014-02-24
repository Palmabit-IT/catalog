<?php
namespace Validators;

use Event;

class ProdottoValidator  extends AbstractValidator{

    protected static $rules = [
        "codice" => ["max:255"],
        "nome" => "required|max:255",
        "slug" => ["required","max:255","AlphaDash"],
        "descrizione" => "max:255",
        "descrizione_estesa" => "max:255",
        "lang" => "max:2",
    ];

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            if(isset($input["id"]))
            {
                static::$rules["slug"][] = "unique:prodotto,slug,{$input['id']}";
            }
        });
    }
} 