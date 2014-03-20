<?php  namespace Palmabit\Catalog\Tests;
use Palmabit\Catalog\Models\RowOrder;
use Palmabit\Catalog\Models\Product;
use Mockery as m;
use App;
/**
 * Test RowOrderTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class RowOrderTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function it_calculates_product_price_below_quantity()
    {
        $row = new RowOrder();
        $product = $this->getStandardProduct();
        $mock_auth = $this->getLoggedUserMock();
        App::instance('authenticator', $mock_auth);
        $expected_price = 24.44;

        $price = $row->calculatePrice($product,2);

        $this->assertEquals($expected_price, $price);

        $mock_auth = $this->getProfessionalUserPriceMock();
        App::instance('authenticator', $mock_auth);
        $expected_price = 10.24;

        $price = $row->calculatePrice($product,2);

        $this->assertEquals($expected_price, $price);

    }

    /**
     * @test
     */
    public function it_calculates_product_price_over_quantity()
    {
        $row = new RowOrder();
        $product = $this->getStandardProduct();
        $mock_auth = $this->getLoggedUserMock();
        App::instance('authenticator', $mock_auth);
        $expected_price = 82.1;

        $price = $row->calculatePrice($product, 10);

        $this->assertEquals($expected_price, $price);

        $mock_auth = $this->getProfessionalUserPriceMock();
        App::instance('authenticator', $mock_auth);
        $expected_price = 21.2;

        $price = $row->calculatePrice($product,10);

        $this->assertEquals($expected_price, $price);
    }

    /**
     * @test
     **/
    public function it_set_an_item_given_a_prdouct()
    {
        $row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()->shouldReceive('calculatePrice')->once()->andReturn(10)->getMock();
        $product = $this->getStandardProduct();

        $row->setItem($product, 10);

        $this->assertEquals(10, $row->quantity);
        $this->assertEquals($product->id, $row->product_id);
        $this->assertEquals(10, $row->total_price);
    }

    /**
     * @test
     * @expectedException Palmabit\Authentication\Exceptions\LoginRequiredException
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
        $product = Product::create([
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
                "price3" => "2.12",
                "quantity_pricing_quantity" => 10,
                "quantity_pricing_enabled" => 1
                ]);
        $row->product_id = $product->id;

        $presenter = $row->getProductPresenter();

        $this->assertInstanceOf('Palmabit\Catalog\Presenters\PresenterProducts', $presenter);
        $this->assertEquals($product->id, $presenter->id);
    }

    protected function getStandardProduct()
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
                    "quantity_pricing_quantity" => 10,
                    "quantity_pricing_enabled" => 1
                    ]);
    }

    protected function getLoggedUserMock()
    {
        return m::mock('StdClass')
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
    }

        public function getProfessionalUserPriceMock()
        {
            return m::mock('StdClass')
                ->shouldReceive('check')
                ->once()
                ->andReturn(true)
                ->shouldReceive('hasGroup')
                ->once()
                ->andReturn(true)
                ->getMock();
        }
}
 