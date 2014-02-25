<?php
namespace Palmabit\Catalog\Validators;

use Event;

class ProductFormOrderValidator  extends AbstractValidator{

    protected static $rules = [
        "order" => "required",
        "id" => "required"
    ];
} 