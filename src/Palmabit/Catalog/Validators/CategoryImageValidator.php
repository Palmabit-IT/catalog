<?php namespace Palmabit\Catalog\Validators;

use Palmabit\Library\Validators\OverrideConnectionValidator;

class CategoryImageValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        'image' => ['required','image','min:1', 'max:4096']
    );
}
