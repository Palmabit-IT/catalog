<?php  namespace Palmabit\Catalog\Tests;
use Palmabit\Catalog\Models\RowOrder;
use Palmabit\Catalog\Models\Product;
use Mockery as m;
use App, Config;
/**
 * Test RowOrderTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class RowOrderTest extends DbTestCase {

    protected $group_professional;
    protected $group_logged;
    protected $row;
    protected $product;
    protected $quantity_professional;
    protected $quantity_non_professional;

    public function setUp()
    {
        parent::setUp();

        $this->group_professional = Config::get('catalog::groups.professional_group_name');
        $this->group_logged = Config::get('catalog::groups.logged_group_name');
        $this->row = new RowOrder();
        $this->quantity_professional = 10;
        $this->quantity_non_professional = 5;
        $this->product = $this->getStandardProduct($this->quantity_professional, $this->quantity_non_professional);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function itSetPrice1()
    {
        $mock_auth = $this->getLoggedUserMock();
        App::instance('authenticator', $mock_auth);
        $expected_single_price = 12.22;
        $expected_price = $expected_single_price * 4;

        $this->row->setItem($this->product,$this->quantity_non_professional-1);

        $this->assertEquals($expected_price, $this->row->total_price);
        $this->assertEquals($expected_single_price, $this->row->single_price);
        $this->assertEquals("price1", $this->row->price_type_used);
    }

    /**
     * @test
     **/
    public function itSetPrice2()
    {
        $mock_auth = $this->getLoggedUserMock();
        App::instance('authenticator', $mock_auth);
        $expected_single_price = 8.21;
        $expected_price = $expected_single_price * 5;

        $this->row->setItem($this->product, $this->quantity_non_professional);

        $this->assertEquals($expected_price, $this->row->total_price);
        $this->assertEquals($expected_single_price, $this->row->single_price);
        $this->assertEquals("price2", $this->row->price_type_used);
    }

   /**
    * @test
    **/
   public function itSetPrice3()
   {
       $mock_auth = $this->getProfessionalUserPriceMock();
       App::instance('authenticator', $mock_auth);
       $expected_single_price = 5.12;
       $expected_price = $expected_single_price * 9;

       $this->row->setItem($this->product,$this->quantity_professional-1);

       $this->assertEquals($expected_price, $this->row->total_price);
       $this->assertEquals($expected_single_price, $this->row->single_price);
       $this->assertEquals("price3", $this->row->price_type_used);
   }

   /**
    * @test
    **/
   public function itSetPrice4()
   {
       $mock_auth = $this->getProfessionalUserPriceMock();
       App::instance('authenticator', $mock_auth);
       $expected_single_price = 2.12;
       $expected_price = $expected_single_price * 10;

       $this->row->setItem($this->product,$this->quantity_professional);

       $this->assertEquals($expected_price, $this->row->total_price);
       $this->assertEquals($expected_single_price, $this->row->single_price);
       $this->assertEquals("price4", $this->row->price_type_used);
   }

    /**
     * @test
     **/
    public function it_set_an_item_given_a_prdouct()
    {
        $row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()->shouldReceive('calculatePrice')
            ->once()
            ->andReturn(10)
            ->shouldReceive('getSingleProductPriceToUse')
            ->once()
            ->andReturn(1)
            ->shouldReceive('getPriceTypeStringToUse')
            ->once()
            ->andReturn('price2')
            ->getMock();
        $product = $this->getStandardProduct($this->quantity_professional,$this->quantity_non_professional);

        $row->setItem($product, 10);

        $this->assertEquals(10, $row->quantity);
        $this->assertEquals($product->id, $row->product_id);
        $this->assertEquals(10, $row->total_price);
    }

    /**
     * @test
     * @expectedException \Palmabit\Authentication\Exceptions\LoginRequiredException
     **/
    public function it_throws_exception_on_calculate_price_if_user_not_logged_in()
    {
        $row = new RowOrder();
        $mock_auth = m::mock('StdClass')->shouldReceive('check')->once()->andReturn(false)->getMock();
        App::instance('authenticator', $mock_auth);

        $row->calculatePrice(new Product,10);
    }

    /**
     * @test
     * @group 1
     **/
    public function it_gets_the_product_presenter()
    {
        $row = new RowOrder();
        $product = $this->getStandardProduct($this->quantity_professional,$this->quantity_non_professional);
        $product->save();
        $row->product_id = $product->id;

        $presenter = $row->getProductPresenter();

        $this->assertInstanceOf('Palmabit\Catalog\Presenters\PresenterProducts', $presenter);
        $this->assertEquals($product->id, $presenter->id);
    }

    protected function getStandardProduct($quantity_professional, $quantity_non_professional)
    {
        return new Product([
                    "description" => "desc",
                    "code" => "code",
                    "name" => "name",
                    "slug" => "slug",
                    "slug_lang" => "",
                    "description_long" => "",
                    "featured" => 1,
                    "public" => 1,
                    "offer" => 1,
                    "stock" => 4,
                    "with_vat" => 1,
                    "video_link" => "http://www.google.com/video/12312422313",
                    "professional" => 1,
                    "price1" => "12.22",
                    "price2" => "8.21",
                    "price3" => "5.12",
                    "price4" => "2.12",
                    "quantity_pricing_quantity" => $quantity_professional,
                    "quantity_pricing_quantity_non_professional" => $quantity_non_professional,
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

    public function getProfessionalUserPriceMock()
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
}
 