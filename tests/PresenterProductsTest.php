<?php  namespace Palmabit\Catalog\Tests; 

use Palmabit\Catalog\Presenters\PresenterProducts;
use Illuminate\Support\Facades\App;
use Palmabit\Catalog\Models\Product;
use Mockery as m;
use Config;
use Palmabit\Catalog\Tests\Traits\ProductStubTrait;

class PresenterProductsTest extends DbTestCase  {

    use ProductStubTrait;

    protected $flags_path;
    protected $group_professional;
    protected $group_logged;
    protected $quantity_professional;
    protected $quantity_non_professional;
    protected $current_lang;
    protected $product_repository;


    public function setUp()
    {
        parent::setUp();
        $this->flags_path = Config::get('catalog::flags.flags_path');
        $this->group_professional = Config::get('catalog::groups.professional_group_name');
        $this->group_logged = Config::get('catalog::groups.logged_group_name');
        $this->quantity_professional = 10;
        $this->quantity_non_professional = 5;
        $this->current_lang = 'it';
        $this->product_repository = App::make('product_repository');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canGetHisFlag()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product), ["lang" => "it"])->first();

        $presenter = new PresenterProducts($product->decorateLanguage('it'));

        $flag = $presenter->flag;

        $expected_flag = "<img class=\"product-flag\" src=\"{$this->flags_path}/it.jpg\" alt=\"it\" />";
        $this->assertEquals($expected_flag, $flag);
    }
    
    /**
     * @test
     **/
    public function canGetAvailableProductFlags()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', array_merge($this->getProductDescriptionStub($product), ["lang" => "it"]))->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', array_merge($this->getProductDescriptionStub($product), ["lang" => "en"]))->first();

        $presenter = new PresenterProducts($product);

        $flags = $presenter->availableflags;

        $expected_flags = "<img class=\"product-flag\" src=\"{$this->flags_path}/it.jpg\" alt=\"it\" /><img class=\"product-flag\" src=\"{$this->flags_path}/en.jpg\" alt=\"en\" />";
        $this->assertEquals($expected_flags, $flags);
    }

    /**
     * @test
     */
    public function it_gets_toggle_status_of_a_tab()
    {
        // test disabled
        $product = new Product();
        $presenter = new PresenterProducts($product);
        $disabled = $presenter->get_toggle;
        $this->assertEquals('data-toggle="" disabled="disabled"', $disabled);
        // test enabled
        $product->exists = true;
        $presenter = new PresenterProducts($product)
        ;
        $enabled= $presenter->get_toggle;
        $this->assertEquals('data-toggle="tab"', $enabled);
    }

    /**
     * @test
     **/
    public function it_gets_all_categories_of_a_product()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $category = $this->make('Palmabit\Catalog\Models\Category', $this->getCategoryStub())->first();
        $this->product_repository->associateCategory($product->id, $category->id);

        $presenter = new PresenterProducts($product);
        $cats = $presenter->categories();

        $this->assertEquals(1, count($cats));
    }

    /**
     * @test
     **/
    public function it_gets_products_products()
    {
        $product_1 = $this->make('Palmabit\Catalog\Models\Product')->first();
        $product_2 = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->product_repository->attachProduct($product_1->id, $product_2->id);

        $presenter = new PresenterProducts($product_1);
        $cats = $presenter->accessories();

        $this->assertEquals(1, count($cats));
    }

    /**
     * @test
     **/
    public function it_obtain_price_small()
    {
        $product   = $this->createAProduct();
        $presenter = new PresenterProducts($product);
        $mock_auth = m::mock('StdClass')
                      ->shouldReceive('check')
                      ->once()
                      ->andReturn(true)
                      ->shouldReceive('hasGroup')
                      ->once()
                      ->andReturn(true)
                      ->getMock();
        App::instance('authenticator', $mock_auth);
        $this->assertEquals("5.12", $presenter->price_small);
        $mock_auth = m::mock('StdClass')
                      ->shouldReceive('check')
                      ->once()
                      ->andReturn(true)
                      ->shouldReceive('hasGroup')
                      ->once()
                      ->andReturn(false)
                      ->shouldReceive('hasGroup')
                      ->once()
                      ->andReturn(true)
                      ->getMock();
        App::instance('authenticator', $mock_auth);
        $this->assertEquals("12.22", $presenter->price_small);
        $mock_auth = m::mock('StdClass')
                      ->shouldReceive('check')
                      ->once()
                      ->andReturn(false)
                      ->getMock();
        App::instance('authenticator', $mock_auth);
        $this->assertEquals("", $presenter->price_small);
    }

    /**
     * @test
     **/
    public function it_obtain_price_big()
    {
        $product   = $this->createAProduct();
        $mock_auth = m::mock('StdClass')
                      ->shouldReceive('check')
                      ->once()
                      ->andReturn(true)
                      ->shouldReceive('hasGroup')
                      ->once()
                      ->andReturn(true)
                      ->getMock();
        App::instance('authenticator', $mock_auth);
        $presenter = new PresenterProducts($product);
        $this->assertEquals("2.12", $presenter->price_big);
        $mock_auth = m::mock('StdClass')
                      ->shouldReceive('check')
                      ->once()
                      ->andReturn(true)
                      ->shouldReceive('hasGroup')
                      ->once()
                      ->andReturn(false)
                      ->shouldReceive('hasGroup')
                      ->once()
                      ->andReturn(true)
                      ->getMock();
        App::instance('authenticator', $mock_auth);
        $presenter = new PresenterProducts($product);
        $this->assertEquals("8.21", $presenter->price_big);
    }

    /**
     * @test
     **/
    public function canObtainQuantityPricingQuantityUsedNonProfessional()
    {
        App::instance('authenticator',$this->getLoggedUserMock() );
        $presenter = new PresenterProducts($this->createAProduct());

        $this->assertEquals($this->quantity_non_professional, $presenter->quantity_pricing_quantity_used);
    }

    public function canObtainQuantityPricingQuantityUsedProfessional()
    {
        App::instance('authenticator',$this->getProfessionalUserMock() );
        $presenter = new PresenterProducts($this->createAProduct());

        $this->assertEquals($this->quantity_professional, $presenter->quantity_pricing_quantity_used);
    }

    /**
     * @test
     **/
    public function it_returns_the_featured_image()
    {
        $presenter = m::mock('Palmabit\Catalog\Presenters\PresenterProducts')->makePartial()->shouldReceive('features')->once()->andReturn(22)->getMock();
        $this->assertEquals(22, $presenter->featured_image);

    }

    /**
     * @test
     **/
    public function it_returns_the_description_and_name()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product))->first();

        $presenter = new PresenterProducts($product);
        $this->assertEquals($product->decorateLanguage()->name, $presenter->name);
        $this->assertExcerptEquals($product, $presenter);
    }

    /**
     * @test
     **/
    public function it_show_IfCanBeBoughtByTheCurrentUser()
    {
        $authenticator = m::mock('StdClass')
                          ->shouldReceive("check")
                          ->andReturn(true)
                          ->shouldReceive('hasGroup')
                          ->andReturn(true)
                          ->getMock();
        App::instance('authenticator', $authenticator);

        $product = $this->createAProduct();
        $presenter = new PresenterProducts($product);
        $can_buy = $presenter->canBeBought();

        $this->assertTrue($can_buy);

        $product->stock= 0;
        $presenter = new PresenterProducts($product);
        $can_buy = $presenter->canBeBought();
        $this->assertFalse($can_buy);
    }

    /**
     * @return Product
     */
    protected function createAProduct()
    {
        return new Product([
                                   "description"               => "desc", "code" => "code",
                                   "name"                      => "name", "slug" => "slug",
                                   "slug_lang"                 => "", "description_long" => "",
                                   "featured"                  => 1, "public" => 1, "offer" => 1,
                                   "stock"                     => 4, "with_vat" => 1,
                                   "video_link"                => "http://www.google.com/video/12312422313",
                                   "professional"              => 1, "price1" => "12.22",
                                   "price2"                    => "8.21", "price3" => "5.12",
                                   "price4"                    => "2.12",
                                   "quantity_pricing_quantity" => 10,
                                   "quantity_pricing_quantity_non_professional" => 5,
                                   "quantity_pricing_enabled" => 1
                           ]);
    }

    protected function getLoggedUserMock()
    {
        return m::mock('StdClass')
                ->shouldReceive('check')
                ->andReturn(true)
                ->shouldReceive('hasGroup')
                ->with($this->group_logged)
                ->andReturn(true)
                ->shouldReceive('hasGroup')
                ->with($this->group_professional)
                ->andReturn(false)
                ->getMock();
    }

    public function getProfessionalUserMock()
    {
        return m::mock('StdClass')
                ->shouldReceive('check')
                ->andReturn(true)
                ->shouldReceive('hasGroup')
                ->with($this->group_logged)
                ->andReturn(false)
                ->shouldReceive('hasGroup')
                ->with($this->group_professional)
                ->andReturn(true)
                ->getMock();
    }

    /**
     * @param $product
     * @param $presenter
     */
    protected function assertExcerptEquals($product, $presenter)
    {
        $this->assertEquals(substr($product->decorateLanguage()->description, 0, 20), substr($presenter->description, 0, 20));
    }
}
 