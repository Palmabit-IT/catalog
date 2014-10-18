<?php  namespace Palmabit\Catalog\Tests;

use Illuminate\Support\Facades\App;
use Palmabit\Catalog\Validators\ProductValidator;
use L;
use Palmabit\Library\Exceptions\ValidationException;

/**
 * Class ProductValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProductValidatorTest extends DbTestCase
{
    protected $product_repository;

    public function setUp()
    {
        parent::setUp();
        $this->product_repository = App::make('product_repository');
    }

    /**
     * @test
     */
    public function itCheckCodeUniquenessInLanguage()
    {
        $code = "12345";
        $this->createProductWithCode($code);

        $validator = new ProductValidator();
        $lang      = 'it';
        $input     = [
            "code"             => $code,
            "name"             => "name",
            "slug"             => "slug",
            "slug_lang"        => "slug",
            "lang"             => $lang,
            "description"      => "desc",
            "long_description" => "",
            "featured"         => 0,
            "public"           => 1,
            "price1"           => "1.00",
            "price3"           => "1.00",
            "offer"            => 0,
            "stock"            => "",
            "form_name"        => "products.general"
        ];
        L::shouldReceive('get_admin')
            ->andReturn($lang);

        $got_exception = false;
        try
        {
            $validator->validate($input);
        }
        catch(ValidationException $e)
        {
            $got_exception = true;
        }

        $this->assertTrue($got_exception);
        $this->assertFalse($validator->getErrors()->isEmpty());
    }

    /**
     * @test
     **/
    public function validateSuccesfullyInput()
    {
        $code = "12345";
        $this->createProductWithCode($code);

        $validator = new ProductValidator();
        $lang = 'it';
        $input     = [
            "id" => 1,
            "code"             => $code,
            "name"             => "name",
            "slug"             => "slug",
            "slug_lang"        => "slug",
            "lang"             => $lang,
            "description"      => "desc",
            "long_description" => "",
            "featured"         => 0,
            "public"           => 1,
            "price1"           => "1.00",
            "price3"           => "1.00",
            "offer"            => 0,
            "stock"            => "",
            "form_name"        => "products.general"
        ];
        L::shouldReceive('get_admin')
         ->andReturn($lang);

        $success = $validator->validate($input);

        $this->assertTrue($success);
    }

    protected function createProductWithCode($code)
    {
        $product_data = ([
            "code"                      => $code,
            "name"                      => "",
            "slug"                      => "",
            "slug_lang"                 => "",
            "lang"                      => 'it',
            "description"               => "",
            "long_description"          => "",
            "featured"                  => 0,
            "public"                    => 1,
            "price1"                    => 1.00,
            "price3"                    => 1.00,
            "offer"                     => 0,
            "stock"                     => "",
            "with_vat"                  => 0,
            "professional"              => 0,
            "quantity_pricing_enabled"  => 0,
            "quantity_pricing_quantity" => 100,
        ]);
        $this->product_repository->create($product_data);
    }
} 