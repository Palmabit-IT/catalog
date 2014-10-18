<?php  namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Presenters\PresenterProducts;
use Palmabit\Catalog\Tests\Traits\StubTrait;
use L;

/**
 * Test ProductTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProductTest extends DbTestCase {
    use StubTrait;
    /**
     * @test
     **/
    public function canCheckForAvailability()
    {
        $product = Product::create([
                                   "code" => "12345",
                                   "name" => "name",
                                   "slug" => "slug",
                                   "slug_lang" => "slug_lang",
                                   "lang" => 'it',
                                   "description" => "",
                                   "long_description" => "",
                                   "featured" => 0,
                                   "public" => 1,
                                   "offer" => 0,
                                   "stock" => 1
                                   ]);
        $this->assertTrue($product->isAvailabile());

        $product->update(["stock" => 0]);

        $this->assertFalse($product->isAvailabile());
    }

    /**
     * @test
     **/
    public function canGetHisPresenter()
    {
        $product_code = "code1";
        $product = new Product(["code" => $product_code]);

        $expected_presenter = new PresenterProducts($product);

        $this->assertEquals($expected_presenter, $product->presenter());
    }

    /**
     * @param $product
     * @param $update_data
     * @return mixed
     */
    protected function assertProductEqualsFormAttributes($product, $attributes_name, $update_data)
    {
        $this->compareFormAttributes('assertEquals', $product, $attributes_name, $update_data);
    }

    /**
     * @param $product
     * @param $update_data
     * @return mixed
     */
    protected function assertProductNotEqualsFormAttributes($product, $attributes_name, $update_data)
    {
        $this->compareFormAttributes('assertNotEquals', $product, $attributes_name, $update_data);
    }

    protected function compareFormAttributes($assert, $product, $attributes_name, $update_data)
    {
        foreach($attributes_name as $attribute_name)
        {
            if(isset($update_data[$attribute_name])) $this->$assert($product->$attribute_name, $update_data[$attribute_name], 'attributo: '.$attribute_name);
        }
        return $attribute_name;
    }

}

class ProductGeneralDataStub extends Product
{
    protected $fillable = [
            "name",
            "code"
    ];

    protected $general_form_attributes = [
            "code",
    ];
}