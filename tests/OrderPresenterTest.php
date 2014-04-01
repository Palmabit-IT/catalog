<?php  namespace Palmabit\Catalog\Tests;
use Carbon\Carbon;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Presenters\OrderPresenter;
use DB, App;
use Mockery as m;
/**
 * Test OrderPresenterTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class OrderPresenterTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_gets_total_order_price()
    {
        $order = $this->prepareOrderData(Carbon::now());

        $presenter = new OrderPresenter($order);
        $expected_total = 44.44;

        $total = $presenter->total_price;

        $this->assertEquals($expected_total, $total);
    }

    /**
     * @test
     **/
    public function it_show_author_email()
    {
        $date = Carbon::now();
        $order = $this->prepareOrderData($date);

        $user_stub = new \StdClass;
        $expected_email = $this->createMockAuthenticatorFindById($user_stub);

        $presenter = new OrderPresenter($order);
        $author_email = $presenter->author_email;

        $this->assertEquals($expected_email,$author_email);
    }


    public function show_europe_formatted_date()
    {
        $date = Carbon::now();
        $order = $this->prepareOrderData($date);
        $presenter = new OrderPresenter($order);

        $date_formatted = $presenter->date;

        $european_Date = 'd-m-Y';
        $this->assertEquals($date->format($european_Date),$date_formatted);
    }

    protected function prepareOrderData($date)
    {
        DB::table('order')->insert([
                               "user_id" => 1,
                               "date" => $date,
                               "created_at" => $date,
                               "updated_at" => $date,
                               "completed" => 1,
                      ]);
        DB::table('row_order')->insert(
            [
                 "order_id" => 1,
                 "product_id" => 1,
                 "quantity" => 222,
                 "total_price" => 22.22,
                 "created_at" => $date,
                 "updated_at" => $date,
            ]
        );
        DB::table('row_order')->insert(
            [
            "order_id" => 1,
            "product_id" => 1,
            "quantity" => 222,
            "total_price" => 22.22,
            "created_at" => $date,
            "updated_at" => $date,
            ]
        );

        return Order::find(1);
    }

    /**
     * @param $user_stub
     * @return string
     */
    protected function createMockAuthenticatorFindById($user_stub)
    {
        $expected_email   = "mail@mail.com";
        $user_stub->email = $expected_email;

        $mock_authenticator = m::mock('StdClass')->shouldReceive('findById')->andReturn($user_stub)->getMock();
        App::instance('authenticator', $mock_authenticator);

        return $expected_email;
    }
}