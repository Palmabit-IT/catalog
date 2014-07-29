<?php  namespace Palmabit\Catalog\Tests;

use Mockery as m;
use L;
use Palmabit\Catalog\ModelMultilanguage\Decorators\ProductLanguageDecorator;

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
    public function canLazyLoadAttributesOnGet()
    {
        
        
    }
    
    /**
     * @test
     **/
    public function canResetLazyLoadedAttribute()
    {
        
    }
    
    //  /**
    //   * @test
    //   **/
    //  public function canSetExistingAttributeInProduct() {
    //    $this->mockLanguageConstructor();
    //    $price = $this->fake->text(5);
    //    $decorator = new ProductDecoratorStub($this->product);
    //
    //    $decorator->price = $price;
    //
    //    $this->assertEquals($this->product->price, $price);
    //  }
    //
    //  /**
    //   * @test
    //   **/
    //  public function canSetLanguageDescriptionInDefaultLanguage() {
    //    $name = $this->fake->text(5);
    //    $price = $this->fake->text(5);
    //    $this->createProductDescription(['lang' => $this->default_lang, 'name' => $name]);
    //    $decorator = new ProductDecoratorStub($this->product);
    //
    //    $decorator->name = $name;
    //    $decorator->price = $price;
    //    $description = $this->product->descriptions[0];
    //
    //    $this->assertCount(1, $this->product->descriptions);
    //    $this->assertEquals($description->name, $name);
    //    $this->assertEquals($this->product->price, $price);
    //  }
    //
    //  /**
    //   * @test
    //   **/
    //  public function canSetLanguageDescriptionToNullObject() {
    //    $this->mockLanguageConstructor();
    //    $name = $this->fake->text(5);
    //    $price = $this->fake->text(5);
    //    $decorator = new ProductDecoratorStub($this->product);
    //
    //    $decorator->name = $name;
    //    $decorator->price = $price;
    //    $description = $this->product->descriptions[0];
    //
    //    $this->assertCount(1, $this->product->descriptions);
    //    $this->assertEquals($description->name, $name);
    //    $this->assertEquals($this->product->price, $price);
    //  }
    //
    //  /**
    //   * @param array $attributes
    //   * @return ProductDescription
    //   */
    //  public function createProductDescription($attributes = []) {
    //    $this->mockLanguageConstructor();
    //    $description = new ProductDescription();
    //    Helper::hydratateObject($attributes, $description);
    //    $this->product->descriptions[] = $description;
    //    return $description;
    //  }
    //
    //  /**
    //   * @test
    //   * @expectedException \Exception
    //   **/
    //  public function needsRequiredField_InOrderToBeIstantiated() {
    //    new ProductDecoratorErrorStub($this->product);
    //  }
    //
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
    //
    //  /**
    //   * @test
    //   **/
    //  public function callMethodsOnResourceIfPresent() {
    //    $product_stub = new ProductStubWithMethod();
    //    $decorator = new ProductDecoratorStub($product_stub);
    //    $fake_param = "test";
    //    $decorator->method($fake_param);
    //
    //    $this->assertTrue($product_stub->has_called_method);
    //    $this->assertEquals($product_stub->param, $fake_param);
    //  }

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
                "name"             => $this->faker->text(20),
                "description"      => $this->faker->text(100),
                "long_description" => $this->faker->text(100),
                "lang"             => $this->current_lang,
                "slug"             => $this->faker->lexify('??????????')
        ];
    }
}

//class ProductDecoratorErrorStub extends ProductLanguageDecorator {
//  protected $null_resource_name = '';
//  protected $descriptions_field_name = '';
//}
//
//class ProductStubWithMethod {
//  public $has_called_method = false;
//  public $param = null;
//
//  public function method($param) {
//    $this->has_called_method = true;
//    $this->param = $param;
//  }
//}