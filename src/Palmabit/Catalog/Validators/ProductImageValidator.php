<?php namespace Palmabit\Catalog\Validators;

use Palmabit\Library\Validators\OverrideConnectionValidator;

class ProductImageValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "description" => "max:255|required",
        'image' => ['required','image','min:1', 'max:4096']
    );
}