<?php  namespace Palmabit\Catalog\Tests;
use Palmabit\Catalog\Models\RowOrder;
use Palmabit\Catalog\Orders\OrderService;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Models\Product;
use Palmabit\Authentication\Models\User;
use Session, App, Event;
use Mockery as m;
/**
 * Test OrderServiceTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class OrderServiceTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_gets_a_new_instance_and_check_for_sessions()
    {
        $this->stopEventCreating();
        $service = new OrderService();
        $order = $service->getOrder();
        $this->assertInstanceOf('Palmabit\Catalog\Models\Order', $order);

        $order = new Order(["user_id" => 1]);
        Session::put($service->getSessionKey(), $order);
        $service = new OrderService();
        $this->assertSame($order, $service->getOrder());
    }
    
    /**
     * @test
     **/
    public function it_add_rows_to_orders_and_update_session()
    {
        $this->stopEventCreating();
        $service = new OrderService();
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
        $quantity = 10;
        $mock_auth = $this->getPriceMockProfessional();
        App::instance('authenticator', $mock_auth);
        $service->addRow($product, $quantity);

        // check for session updated data
        $order_session = Session::get($service->getSessionKey());
        $this->assertEquals(1, $order_session->getRowOrders()->count());
    }

    /**
     * @test
     **/
    public function it_saves_db_data_on_commit_and_clear_session()
    {
        $this->stopEventCreating();
        $service = new OrderService();
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
        $user_stub = new User;
        $user_stub->id = 1;
        // mock authenticator
        $mock_auth = m::mock('StdClass')
            ->shouldReceive('check')
            ->once()
            ->andReturn(true)
            ->shouldReceive('hasGroup')
            ->once()
            ->andReturn(true)
            ->shouldReceive('getLoggedUser')
            ->andReturn($user_stub)
            ->getMock();
        $quantity = 10;
        App::instance('authenticator', $mock_auth);
        $service->addRow($product, $quantity);
        // mock mailer
        $mock_mailer = m::mock('Palmabit\Library\Email\MailerInterface')->shouldReceive('sendTo')->andReturn(true)->getMock();
        App::instance('palmamailer', $mock_mailer);
        // mock auth helper
        $mock_auth_helper = m::mock('StdClass')->shouldReceive('getNotificationRegistrationUsersEmail')->once()->andReturn([""])->getMock();
        App::instance('authentication_helper', $mock_auth_helper);

        $service->commit();
        $row = RowOrder::first();
        $this->assertEquals(1, $row->id);

        $this->assertFalse(Session::has($service->getSessionKey()));
    }

    /**
     * @test
     **/
    public function it_send_email_to_user_and_admin_on_commit()
    {
        $user_stub = new User();
        $user_stub->id = 1;
        $mock_auth = m::mock('StdClass')->shouldReceive('getLoggedUser')->andReturn($user_stub)->getMock();
        App::instance('authenticator', $mock_auth);
        $service = new OrderService();
        $mock_mailer = m::mock('Palmabit\Library\Email\MailerInterface')->shouldReceive('sendTo')->andReturn(true)->getMock();
        $mock_auth_helper = m::mock('StdClass')->shouldReceive('getNotificationRegistrationUsersEmail')->once()->andReturn([""])->getMock();
        App::instance('authentication_helper', $mock_auth_helper);
        $mock_mailer = m::mock('Palmabit\Library\Email\MailerInterface')->shouldReceive('sendTo')->andReturn(true)->getMock();
        App::instance('palmamailer', $mock_mailer);

        $service->sendEmailToClient();
        $service->sendEmailToAdmin();
    }

    /**
     * @test
     **/
    public function it_deletes_row()
    {
        $mock_order = m::mock('StdClass')->shouldReceive('deleteRowOrder')
            ->once()
            ->andReturn(true)
            ->getMock();
        $service = new OrderServiceStub($mock_order);

        $service->deleteRow(1);
    }

    /**
     * @test
     **/
    public function it_changes_row_quantity()
    {
        $mock_order = m::mock('StdClass')->shouldReceive('changeRowQuantity')
            ->once()
            ->andReturn(true)
            ->getMock();
        $service = new OrderServiceStub($mock_order);

        $service->changeRowQuantity(1,10);
    }

    /**
     * @test
     * @expectedException Palmabit\Authentication\Exceptions\LoginRequiredException
     **/
    public function it_throws_exception_if_cannot_find_the_user_email()
    {
        $mock_auth = m::mock('StdClass')->shouldReceive('getLoggedUser')->andReturn(false)->getMock();
        App::instance('authenticator', $mock_auth);
        $mock_mailer = m::mock('Palmabit\Library\Email\MailerInterface')->shouldReceive('sendTo')->andReturn(true)->getMock();

        $service = new OrderService();
        $service->sendEmailToClient($mock_mailer);
    }

    protected function getPriceMockProfessional()
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

    protected function stopEventCreating()
    {
        Event::listen('order.creating', function(){return false;});
    }


}

class OrderServiceStub extends OrderService
{
    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }
}