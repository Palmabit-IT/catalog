<?php  namespace Palmabit\Catalog\Tests;

use Mockery as m;
use L;
use Palmabit\Catalog\ModelMultilanguage\Decorators\ProductLanguageDecorator;
use Palmabit\Catalog\Models\Product;

class ProductLanguageDecoratorTest extends DbTestCase
{

    private $product;
    private $current_lang;
    private $default_lang;
    protected $product_description;
    protected $default_product_description;

    public function setUp()
    {
        parent::setUp();
        $this->current_lang = 'en';
        $this->default_lang = 'it';
        $this->product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->product_description = $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($this->product))
                                          ->first();
        $this->default_product_description =
                $this->make('Palmabit\Catalog\Models\ProductDescription', array_merge($this->getProductDescriptionStub($this->product), ['lang' => $this->default_lang]))
                     ->first();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canWrapProduct()
    {
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);
        $this->assertSame($this->product, $decorator->getResource());
    }

    /**
     * @test
     **/
    public function canGetNullAttribute()
    {
        $this->current_lang = 'es';
        $this->default_lang = 'es';
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);

        $this->assertEquals('', $decorator->name);
    }

    /**
     * @test
     **/
    public function canGetNotCustomLanguageAttribute()
    {
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);

        $this->assertEquals($this->product->code, $decorator->code);
    }

    /**
     * @test
     **/
    public function canGetCurrentLanguageAttribute()
    {
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);

        $this->assertEquals($this->product_description->name, $decorator->name);
    }

    /**
     * @test
     **/
    public function canGetDefaultLanguageDescriptionIfExists()
    {
        $this->current_lang = 'es';
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);

        $this->assertEquals($this->default_product_description->name, $decorator->name);
    }

    /**
     * @test
     **/
    public function canSetExistingAttributeInProduct()
    {
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);

        $new_code = $this->faker->unique()->lexify('??????????');
        $decorator->code = $new_code;

        $this->assertEquals($this->product->code, $new_code);
    }

    /**
     * @test
     **/
    public function canSetLanguageDescriptionInDefaultLanguage()
    {
        $this->current_lang = $this->default_lang;
        $this->mockLanguageConstructor();
        $new_name = $this->faker->unique()->text(5);
        $decorator = new ProductLanguageDecorator($this->product);

        $decorator->name = $new_name;
        $description = $this->product->language_descriptions->last();

        $this->assertEquals($description->name, $new_name);
    }

    /**
     * @test
     **/
    public function canSetLanguageDescriptionToNullObject()
    {
        $this->current_lang = 'es';
        $this->mockLanguageConstructor();
        $new_name = $this->faker->text(5);
        $decorator = new ProductLanguageDecorator($this->product);

        $decorator->name = $new_name;
        $description = $this->product->language_descriptions->last();

        $this->assertEquals(3, $this->product->language_descriptions->count(), 'The product doesn\'t have the right number of descriptions');
        $this->assertEquals($description->name, $new_name);
        $this->assertEquals($this->current_lang, $description->lang);
        $this->assertEquals($this->product->id, $description->product_id);
    }

    /**
     * @test
     * @expectedException \Exception
     **/
    public function needsRequiredField_InOrderToBeIstantiated()
    {
        new ProductDecoratorErrorStub($this->product);
    }

    /**
     * @param $current_lang
     * @param $default_lang
     */
    public function mockLanguageConstructor()
    {
        L::shouldReceive('get')
         ->once()
         ->andReturn($this->current_lang)
         ->shouldReceive('getDefault')
         ->once()
         ->andReturn($this->default_lang);
    }

    /**
     * @test
     **/
    public function callMethodsOnResourceIfPresent()
    {
        $product_stub = new ProductStubWithMethod();
        $decorator = new ProductLanguageDecorator($product_stub);
        $fake_param = "test";
        $decorator->method($fake_param);

        $this->assertTrue($product_stub->has_called_method);
        $this->assertEquals($product_stub->param, $fake_param);
    }

    /**
     * @test
     **/
    public function canRemoveGivenLanguageDescription()
    {
        $decorator = new ProductLanguageDecorator($this->product);
        $decorator->removeLanguageDescription("it");
        $decorator->save();

        $this->assertProductHasDescriptionsCount(1, $this->product);
        $this->assertEquals($this->product->language_descriptions->first()->lang, "en");
    }

    /**
     * @test
     **/
    public function canPersistNewLanguageData()
    {
        $this->current_lang = 'es';
        $this->mockLanguageConstructor();
        $new_name = $this->faker->text(5);
        $decorator = new ProductLanguageDecorator($this->product);
        $decorator->name = $new_name;

        $this->product->save();

        $description_created = $this->product_description->whereLang($this->current_lang)->firstOrFail();
        $this->assertEquals($description_created->name, $new_name);
        $this->assertProductHasDescriptionsCount(3, $this->product);
    }

    /**
     * @test
     **/
    public function canUpdateOldLanguageData()
    {
        $this->mockLanguageConstructor();
        $new_name = $this->faker->text(5);
        $decorator = new ProductLanguageDecorator($this->product);
        $decorator->name = $new_name;

        $this->product->save();

        $description_created = $this->product_description->whereLang($this->current_lang)->firstOrFail();
        $this->assertEquals($description_created->name, $new_name);
        $this->assertProductHasDescriptionsCount(2, $this->product);
    }

    /**
     * @test
     **/
    public function canFillAllData()
    {
        $this->mockLanguageConstructor();
        $decorator = new ProductLanguageDecorator($this->product);
        $data = [
            "name" => $this->faker->unique()->text(20),
            "code" => $this->faker->unique()->lexify('????????'),
        ];

        $decorator->fill($data)->save();

        $this->assertEquals($this->product->code, $data["code"]);
        $description_updated = $this->product_description->whereLang($this->current_lang)->firstOrFail();
        $this->assertEquals($description_updated->name, $data["name"]);
    }
    
    /**
     * @test
     **/
    public function canBeCreatedWithCustomCurrentLanguage()
    {
        $this->mockLanguageConstructor();
        $current_language = "us";
        $decorator = new ProductLanguageDecorator($this->product, $current_language);

        $this->assertEquals($decorator->getCurrentLang(), $current_language);
    }
    
    protected function getModelStub()
    {
        return [
                "code" => $this->faker->lexify('????????????'),
        ];
    }

    protected function getProductDescriptionStub($product)
    {
        return [
                "product_id"       => $product->id,
                "name"             => $this->faker->unique()->text(20),
                "description"      => $this->faker->unique()->text(100),
                "long_description" => $this->faker->unique()->text(100),
                "lang"             => $this->current_lang,
                "slug"             => $this->faker->unique()->lexify('??????????')
        ];
    }

    /**
     * @param $istances
     * @param $product
     */
    private function assertProductHasDescriptionsCount($istances, $product)
    {
        $relation_name = $this->product->getLanguageDescriptionsRelation();
        $this->assertEquals($istances, $product->$relation_name()->get()->count());
    }
}

class ProductDecoratorErrorStub extends ProductLanguageDecorator
{
    protected $null_resource_name = '';
    protected $descriptions_relation_name = '';
}

class ProductStubWithMethod extends Product
{
    public $has_called_method = false;
    public $param = null;

    public function method($param)
    {
        $this->has_called_method = true;
        $this->param = $param;
    }
}