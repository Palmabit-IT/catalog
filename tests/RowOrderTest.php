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
    public function it_calculates_product_price()
    {
        $row = new RowOrder();
        $product = new Product([
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
        $mock_auth = $this->getLoggedUserMock();
        App::instance('authenticator', $mock_auth);
        $expected_price = "12.22";

        $price = $row->calculatePrice($product,1);

        $this->assertEquals($expected_price, $price);
    }
    
    /**
     * @test
     **/
    public function it_trhow_exception_on_calculate_price_if_user_not_logged_in()
    {
        
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

    public function getProfessionalUserPrice()
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
 