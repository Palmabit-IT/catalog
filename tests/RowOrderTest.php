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
    public function it_set_item_price_and_saved_row_data()
    {
        $row = new RowOrder();
        $product = $this->getStandardProduct();
        $mock_auth = $this->getLoggedUserMock();
        App::instance('authenticator', $mock_auth);
        $expected_price = 11.00;

        $price = $row->calculatePrice($product, 10);

        $this->assertEquals($expected_price, $price);
    }

    /**
     * @test
     **/
    public function it_set_an_item_given_a_product()
    {
        $row = new RowOrder();
        $product = $this->getStandardProduct();
        $login_mock = $this->getLoggedUserMock();
        App::instance('authenticator',$login_mock);

        $quantity = 10;
        $row->setItem($product, $quantity);

        $this->assertEquals($quantity, $row->quantity);
        $this->assertEquals($product->id, $row->product_id);
        $expected_total_price = 11.00;
        $this->assertEquals($expected_total_price, $row->total_price);
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
        $product = $this->getStandardProduct();
        $product->save();
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
                    "video_link" => "http://www.google.com/video/12312422313",
                    "price" => "1.10",
                    ]);
    }

    protected function getLoggedUserMock()
    {
        return m::mock('StdClass')
            ->shouldReceive('check')
            ->once()
            ->andReturn(true)
            ->getMock();
    }
}
 