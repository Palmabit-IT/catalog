<?php
namespace Palmabit\Catalog\Validators;

use Event;
use Palmabit\Library\Validators\AbstractValidator;

class ProductFormOrderValidator  extends AbstractValidator{

    protected static $rules = [
        "order" => "required",
        "id" => "required"
    ];
} 