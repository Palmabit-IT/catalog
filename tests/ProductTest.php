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
     * @test
     **/
    public function fillsOnlyGeneralAttributes_WhenNoDefaultLanguage()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product', $this->getProductModelStub())->first();
        $update_data = [
                "code"             => $this->faker->unique()->text(5),
                "name"             => $this->faker->unique()->text(10),
                "slug"             => $this->faker->unique()->text(5),
                "slug_lang"        => $this->faker->unique()->text(10),
                "description"      => $this->faker->text(10),
                "long_description" => $this->faker->text(100),
                "featured"         => ($product->featured) ? false : true,
                "public"           => ($product->public) ? false : true,
                "offer"            => ($product->offer) ? false : true
        ];
        L::shouldReceive('getDefault')->once()->andReturn('en');

        $product->setGeneralFormFilterEnabled(true)->update($update_data);

        $this->assertProductEqualsFormAttributes($product, $product->getGeneralFormAttributes(), $update_data);

        $this->assertProductNotEqualsFormAttributes($product, array_diff($product->getFillable(), $product->getGeneralFormAttributes()), $update_data);
    }

    /**
     * @test
     **/
    public function fillsAllAttributes_WhenDefaultLanguage()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product', $this->getProductModelStub())->first();
        $update_data = [
                "code"             => $this->faker->unique()->text(5),
                "name"             => $this->faker->unique()->text(10),
                "slug"             => $this->faker->unique()->text(5),
                "slug_lang"        => $this->faker->unique()->text(10),
                "description"      => $this->faker->text(10),
                "long_description" => $this->faker->text(100),
                "featured"         => ($product->featured) ? false : true,
                "public"           => ($product->public) ? false : true,
                "offer"            => ($product->offer) ? false : true
        ];

        L::shouldReceive('getDefault')->once()->andReturn('it');

        $product->setGeneralFormFilterEnabled(true)->update($update_data);

        $this->assertProductEqualsFormAttributes($product, $product->getFillable(), $update_data);
    }

    /**
     * @test
     **/
    public function canGetUniqueData()
    {
        $expected_data = ["name"];

        $product = new ProductGeneralDataStub();

        $this->assertEquals($expected_data, $product->getUniqueData());
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