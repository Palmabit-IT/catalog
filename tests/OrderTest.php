<?php  namespace Palmabit\Catalog\Tests;
use Mockery as m;
use App;
use Carbon\Carbon;
use Palmabit\Library\Exceptions\ValidationException;
use Palmabit\Authentication\Models\User;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Models\RowOrder;

/**
 * Test OrderTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class OrderTest extends DbTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_add_row_order_to_his_collection()
    {
        $order = new Order;
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
        $mock_row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()
            ->shouldReceive('setItem')
            ->once()
            ->with($product, 10)
            ->andReturn(true)
            ->getMock();
        $order->addRow($product, 10, $mock_row);

        $this->assertEquals(1, $order->getRowOrders()->count());
    }

    /**
     * @test
     **/
    public function it_saves_himself_and_his_collection_to_db_and_set_completed_and_user_id()
    {
        $order = new Order;
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
        $mock_row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()
            ->shouldReceive('setItem')
            ->once()
            ->with($product, 10)
            ->andReturn(true)
            ->getMock();
        $mock_row->product_id = 10;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, 10, $mock_row);
        $user_stub = new User();
        $user_stub->id = 10;
        $mock_auth = m::mock('StdClass')->shouldReceive('getLoggedUser')->once()->andReturn($user_stub)->getMock();
        App::instance('authenticator',$mock_auth);

        $order->save();

        // set completed state
        $this->assertTrue($order->completed);
        // set user_id
        $this->assertEquals(10,$order->user_id);
        // save his collection
        $row = RowOrder::first();
        $this->assertEquals(10, $row->product_id);
        $this->assertEquals($order->id, $order->getRowOrders()->first()->order_id);
    }

    /**
     * @test
     **/
    public function it_deletes_a_row_given_product_id()
    {
        $order = new Order;
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
        $mock_row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()
            ->shouldReceive('setItem')
            ->once()
            ->andReturn(true)
            ->getMock();
        $mock_row->product_id = 1;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, 10, $mock_row);

        $order->deleteRowOrder(1);

        $this->assertEquals(0, $order->getRowOrders()->count());
    }

    /**
     * @test
     **/
    public function it_change_quantity_of_an_order()
    {
        $order = new Order;
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
        $mock_row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()
            ->shouldReceive('setItem')
            ->once()
            ->andReturn(true)
            ->getMock();
        $mock_row->product_id = 1;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, 10, $mock_row);
        // mock price calculation
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
        App::instance("authenticator", $mock_auth);
        $order->changeRowQuantity(1,3);

        $this->assertEquals(3, $order->getRowOrders()->first()->quantity);
    }
    
    /**
     * @test
     * @group 1
     **/
    public function it_validate_row_orders()
    {
        $order = new Order;

        $this->assertFalse($order->validate());

        $this->assertFalse($order->getErrors()->isEmpty());
    }

    /**
     * @test
     * @expectedException Palmabit\Library\Exceptions\ValidationException
     **/
    public function it_throws_exception_on_save_if_validation_fails()
    {
        $order = new Order;

        $order->save();
    }

    /**
     * @test
     **/
    public function it_set_errors_on_save_if_validation_fails()
    {
        $order = new Order;
        try
        {
            $order->save();
        }
        catch(ValidationException $e)
        {}

        $this->assertFalse($order->getErrors()->isEmpty());
    }

    /**
     * @test
     * @expectedException Palmabit\Library\Exceptions\NotFoundException
     **/
    public function it_throw_exception_if_cannot_delete_an_item()
    {
        $order = new Order;
        $order->deleteRowOrder(1);
    }

    /**
     * @test
     * @expectedException Palmabit\Library\Exceptions\NotFoundException
     **/
    public function it_throw_exception_if_cannot_change_quantity_of_an_item()
    {
        $order = new Order;
        $order->changeRowQuantity(1,10);
    }

    /**
     * @test
     * @expectedException Palmabit\Authentication\Exceptions\LoginRequiredException
     **/
    public function it_throws_exception_if_no_user_is_logged()
    {
        $order = new Order();
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
        $mock_row = m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()
            ->shouldReceive('setItem')
            ->once()
            ->with($product, 10)
            ->andReturn(true)
            ->getMock();
        $mock_row->product_id = 10;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, 10, $mock_row);
        $order->save();
    }

}
 