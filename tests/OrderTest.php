<?php  namespace Palmabit\Catalog\Tests;

use Mockery as m;
use App, DB;
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
        $product  = $this->createAProduct();

        $quantity = 10;
        $mock_row = $this->mockRowOrderSetItem($product,$quantity);
        $order->addRow($product, 10, $mock_row);

        $this->assertEquals(1, $order->getRowOrders()->count());
    }

    /**
     * @test
     **/
    public function it_saves_himself_and_his_collection_to_db_and_set_completed_and_user_id()
    {
        $order = new Order;
        $product  = $this->createAProduct();

        $quantity = 10;
        $mock_row = $this->mockRowOrderSetItem($product,$quantity);
        $mock_row->product_id = 10;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $mock_row->single_price = 1.00;
        $order->addRow($product, 10, $mock_row);

        $user_id = 10;
        $this->mockAuthenticatorReturnUserWithId($user_id);

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
    public function it_clear_existing_product_and_update_quantity()
    {
        $order = new Order;
        $product = $this->createAndSaveProduct();

        $first_quantity = 10;
        $mock_row = $this->mockRowOrderSetItem($product,$first_quantity);

        $order->addRow($product, $first_quantity, $mock_row);
        $mock_row->quantity = $first_quantity;
        $mock_row->slug_lang = $product->slug_lang;

        $second_quantity = 20;
        $quantity = $order->clearDuplicatesAndUpdateQuantity($product, $second_quantity);

        $this->assertEquals(30, $quantity);
        $this->assertEquals(0, $order->getRowOrders()->count());
    }

    protected function createAndSaveProduct()
    {
        return Product::create([
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
                    "price" => "12.22",
                    ]);
    }

    /**
     * @test
     **/
    public function it_deletes_a_row_given_product_id()
    {
        $order = new Order;
        $product = $this->createAndSaveProduct();
        $quantity = 10;
        $mock_row = $this->mockRowOrderSetItem($product,$quantity);
        $mock_row->product_id = 1;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, $quantity, $mock_row);

        $order->deleteRowOrder(1);

        $this->assertEquals(0, $order->getRowOrders()->count());
    }

    /**
     * @test
     **/
    public function it_change_quantity_of_an_order()
    {
        $order = new Order;
        $product = $this->createAndSaveProduct();
        $quantity = 10;
        $mock_row = $this->mockRowOrderSetItem($product, $quantity);
        $mock_row->product_id = 1;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, $quantity, $mock_row);
        $this->mockAuthLoggedIn();

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
     **/
    public function it_calculate_total_amount()
    {
        $order_repository = App::make('order_repository');
        DB::table('order')->insert([
                                  "user_id" => 1,
                                  "date" => Carbon::now(),
                                  "completed" => 1,
                                  "created_at" => Carbon::now(),
                                  "updated_at" => Carbon::now(),
                                  ]);

        $this->createTwoOrderWithPrice(11.00,21.00);
        $expected_total = 32.00;

        $order_id =1;
        $order = $order_repository->find($order_id);
        $total = $order->calculateTotalAmount();

        $this->assertEquals($expected_total, $total);
    }

    private function createTwoOrderWithPrice($price1, $price2)
    {
        $fist_row = [
            "order_id" => 1,
            "product_id" => 1,
            "quantity" => 1,
            "total_price" => $price1,
            "single_price" => $price1,
        ];
        $second_row = [
            "order_id" => 1,
            "product_id" => 1,
            "quantity" => 1,
            "total_price" => $price2,
            "single_price" => $price2,
        ];
        RowOrder::create($fist_row);
        RowOrder::create($second_row);
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
        $product = $this->createAndSaveProduct();
        $quantity = 10;
        $mock_row = $this->mockRowOrderSetItem($product, $quantity);
        $mock_row->product_id = 10;
        $mock_row->quantity= 1;
        $mock_row->total_price = 1.00;
        $order->addRow($product, $quantity, $mock_row);
        $order->save();
    }

    /**
     * @test
     **/
    public function it_getsThePresenterOfTheOrder()
    {
        $order = new Order;

        $presenter = $order->getPresenter();

        $this->assertInstanceOf('Palmabit\Catalog\Presenters\OrderPresenter', $presenter);
        $this->assertEquals($order, $presenter->getResource());
    }

    /**
     * @param $product
     * @return mixed
     */
    private function mockRowOrderSetItem($product, $quantity)
    {
        return m::mock('Palmabit\Catalog\Models\RowOrder')->makePartial()->shouldReceive('setItem')->once()->with($product, $quantity)->andReturn(true)->getMock();
    }

    /**
     * @return Product
     */
    private function createAProduct()
    {
        $product = new Product([
                               "description" => "desc", "code" => "code", "name" => "name",
                               "slug"        => "slug", "slug_lang" => "", "description_long" => "",
                               "featured"    => 1, "public" => 1, "offer" => 1, "stock" => 4,
                               "video_link"  => "http://www.google.com/video/12312422313",
                               "price"       => "1.00",
                               ]);
        return $product;
    }

    private function mockAuthenticatorReturnUserWithId($id)
    {
        $user_stub     = new User();
        $user_stub->id = $id;
        $mock_auth     = m::mock('StdClass')->shouldReceive('getLoggedUser')->once()->andReturn($user_stub)->getMock();
        App::instance('authenticator', $mock_auth);
    }

    private function mockAuthLoggedIn()
    {
        $mock_auth = m::mock('StdClass')->shouldReceive('check')->once()->andReturn(true)->getMock();
        App::instance("authenticator", $mock_auth);
    }

}
 