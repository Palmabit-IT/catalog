<?php namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class CategoryValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "name" => "required|max:255",
    );
}
