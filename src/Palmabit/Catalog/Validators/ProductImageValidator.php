<?php namespace Validators;

class ProductImageValidator extends AbstractValidator
{
    protected static $rules = array(
        "description" => "max:255",
        'data' => ['required','image','min:1', 'max:4096']
    );
}