<?php
namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class ProductFormOrderValidator  extends OverrideConnectionValidator{

    protected static $rules = [
        "order" => "required",
        "id" => "required"
    ];
} 