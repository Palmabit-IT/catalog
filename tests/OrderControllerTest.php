<?php  namespace Palmabit\Catalog\Tests; 
use Mockery as m;
use App;
use Palmabit\Authentication\Models\User;
use Palmabit\Catalog\Models\Order;

/**
 * Test OrderControllerTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class OrderControllerTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_lists_all_products()
    {
        $this->action('GET', 'Palmabit\Catalog\Controllers\OrderController@lists');

        $this->assertResponseOk();
    }

    /**
     * @test
     **/
    public function it_show_detail_of_a_product()
    {
        $this->mockOrderRepository();

        $this->mockAuthenticatorFindById();

        $this->action('GET', 'Palmabit\Catalog\Controllers\OrderController@show',['id' => 1]);

        $this->assertResponseOk();
        $this->assertViewHas('order_presenter');
        $this->assertViewHas('order');
    }

    protected function mockOrderRepository()
    {
        $order = new Order();
        $order->date = \Carbon\Carbon::now();
        $mock_repo = m::mock('StdClass')->shouldReceive('find')->once()->andReturn(new Order($order))->getMock();
        App::instance('order_repository', $mock_repo);
    }

    protected function mockAuthenticatorFindById()
    {
        $mock_auth = m::mock('StdClass')->shouldReceive('findById')->andReturn(new User())->getMock();
        App::instance('authenticator', $mock_auth);
    }

}
 