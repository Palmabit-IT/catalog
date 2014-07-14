<?php  namespace Palmabit\Catalog\Validators;

use Illuminate\Support\Facades\Event;
use Palmabit\Library\Validators\AbstractValidator;

class CategoryDescriptionValidator extends AbstractValidator
{
    protected static $rules = [
        "slug" => ["required"],
        "description" => ["required","max:255"],
    ];

    public function __construct()
    {
        Event::listen('validating', function ($input)
        {
            if(isset($input["id"]))
            {
                static::$rules["slug"][] = "unique:category_description,slug,{$input['id']}";
            }
        });
    }
}