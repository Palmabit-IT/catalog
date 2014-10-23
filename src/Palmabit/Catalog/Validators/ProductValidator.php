<?php
namespace Palmabit\Catalog\Validators;

use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;
use L;
use Palmabit\Catalog\Models\ProductDescription;
use Palmabit\Library\Exceptions\ValidationException;
use Palmabit\Library\Validators\OverrideConnectionValidator;

class ProductValidator  extends OverrideConnectionValidator{

    protected static $rules = [
        "code" => ["max:255"],
        "name" => "required|max:255",
        "slug" => ["required","max:255","AlphaDash"],
        "description" => "required|max:60",
        "description_long" => "max:8000",
        "lang" => "max:2",
        "video_link" => "max:255|url",
        "price1" => "currency",
        "price2" => "currency",
        "price3" => "currency",
        "price4" => "currency",
        'quantity_pricing_quantity' => 'integer|max:1000000',
        'quantity_pricing_quantity_non_professional' => 'integer|max:1000000'
    ];

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            if(! isset($input['form_name']) || $input['form_name'] != 'products.general') return true;

            if(App::environment() != 'testing' && isset($input["id"]))
            {
                $except_id = ProductDescription::where('product_id','=', $input['id'])
                        ->whereLang(L::get_admin())
                        ->pluck('id');
                static::$rules["slug"][] = "unique:product_description,slug,{$except_id}";
            }

            $found = false;
            try
            {
                $found = App::make('product_repository')->findByCodeAndLang($input['code'],L::get_admin());
                // ignore found if it's an update of the same row
                if(isset($input['id']) && $found->id == $input['id']) $found = false;
            }
            catch(ModelNotFoundException $e)
            {}
            if ($found)
            {
                $this->errors = new MessageBag(["code" => "codice già presente in questa lingua."]);
                throw new ValidationException;
            }
        });
    }
} 