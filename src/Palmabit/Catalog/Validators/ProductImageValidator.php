<?php namespace Palmabit\Catalog\Validators;

use Palmabit\Library\Validators\AbstractValidator;

class ProductImageValidator extends AbstractValidator
{
    protected static $rules = array(
        "description" => "max:255",
        'image' => ['required','image','min:1', 'max:4096']
    );
}