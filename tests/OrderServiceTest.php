<?php  namespace Palmabit\Catalog\Tests;
use Palmabit\Authentication\Exceptions\LoginRequiredException;
use Palmabit\Library\Exceptions\InvalidException;
use Palmabit\Library\Exceptions\PalmabitExceptionsInterface;
use Palmabit\Catalog\Models\RowOrder;
use Palmabit\Catalog\Orders\OrderService;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Models\Product;
use Palmabit\Authentication\Models\User;
use Session, App;
use Mockery as m;
/**
 * Test OrderServiceTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class OrderServiceTest extends DbTestCase {

    protected $order_service;

    public function setUp()
    {
        $this->order_service = new OrderService();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_gets_a_new_instance_and_check_for_sessions()
    {
        $order = $this->order_service->getOrder();

        $this->assertInstanceOf('Palmabit\Catalog\Models\Order', $order);
    }

    /**
     * @test
     **/
    public function itObtainTheOrderInSession()
    {
        $order = new Order(["user_id" => 1]);

        $this->saveInOrderSession($order);
        $order_service = new OrderService;
        $this->assertSame($order, $order_service->getOrder());
    }


    /**
     * @param $order
     */
    private function saveInOrderSession($order)
    {
        Session::put($this->order_service->getSessionKey(), $order);
    }

    /**
     * @test
     **/
    public function it_add_rows_to_orders_and_update_session()
    {
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
                               "video_link" => "http://www.google.com/video/12312422313",
                               "price" => "12.22",
                               ]);
        $quantity = 10;
        $mock_auth = $this->getAuthCheckMock();
        App::instance('authenticator', $mock_auth);

        $this->order_service->addRow($product, $quantity);

        $order_session = $this->getOrderSession();
        $number_of_rows = 1;
        $this->assertEquals($number_of_rows, $order_session->getRowOrders()->count());
    }

    /**
     * @test
     **/
    public function it_saves_db_data_on_commit_and_clear_session()
    {
        $service = new OrderServiceStub();
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
                               "video_link" => "http://www.google.com/video/12312422313",
                               "price" => "1.12",
                               ]);
        $user_stub = new User;
        $user_stub->id = 1;

        // mock authenticator
        $mock_auth = m::mock('StdClass')
            ->shouldReceive('check')
            ->once()
            ->andReturn(true)
            ->shouldReceive('getLoggedUser')
            ->andReturn($user_stub)
            ->shouldReceive('getLoggedUserProfile')
            ->andReturn([])
            ->getMock();
        App::instance('authenticator', $mock_auth);
        $quantity = 10;
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
        $this->assertFalse(Session::has($service->getSessionMailOrder()));
    }

    /**
     * @test
     **/
    public function it_send_email_to_user_and_admin_on_commit()
    {
        $user_id = 1;
        $this->MockAuthenticatorToReturnUserWithId($user_id);
        $service = new OrderService();

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
    public function it_set_error_and_throw_exception_if_mail_client_fails()
    {
        $user_id = 1;
        $this->MockAuthenticatorToReturnUserWithId($user_id);

        $mock_auth_helper = m::mock('StdClass')->shouldReceive('getNotificationRegistrationUsersEmail')->andReturn([""])->getMock();
        App::instance('authentication_helper', $mock_auth_helper);

        $mock_order = m::mock('Palmabit\Catalog\Models\Order')->makePartial()->shouldReceive('save')->getMock();
        $service = new OrderServiceStub($mock_order);

        $mock_mailer = m::mock('Palmabit\Library\Email\MailerInterface')->shouldReceive('sendTo')->andThrow(new LoginRequiredException)->getMock();
        App::instance('palmamailer', $mock_mailer);

        $gotcha = false; // if get the exceptions
        try
        {
            $service->commit();
        }
        catch(InvalidException $e)
        {
            $gotcha = true;
        }
        $this->assertTrue($gotcha);
        $this->assertFalse($service->getErrors()->isEmpty());
    }

    /**
     * @test
     **/
    public function it_set_error_and_throw_exception_if_mail_admin_fails()
    {
        $user_id = 1;
        $this->MockAuthenticatorToReturnUserWithId($user_id);
        $mock_order = m::mock('Palmabit\Catalog\Models\Order')->makePartial()->shouldReceive('save')->getMock();
        $service = new OrderServiceStub($mock_order);
        $mock_mailer = m::mock('Palmabit\Library\Email\MailerInterface')->shouldReceive('sendTo')
            ->once()
            ->andReturn(true)
            ->shouldReceive('sendTo')
            ->once()
            ->andThrow(new LoginRequiredException)
            ->getMock();
        App::instance('palmamailer', $mock_mailer);

        $mock_auth_helper = m::mock('StdClass')->shouldReceive('getNotificationRegistrationUsersEmail')->once()->andReturn([""])->getMock();
        App::instance('authentication_helper', $mock_auth_helper);

        $gotcha = false; // if get the exceptions
        try
        {
        $service->commit();
        }
        catch(InvalidException $e)
        {
            $gotcha = true;
        }

        $this->assertTrue($gotcha);
        $this->assertFalse($service->getErrors()->isEmpty());
    }

    /**
     * @test
     **/
    public function it_set_error_and_throw_exception_if_save_not_succeed()
    {
        $service = new OrderService();
        $gotcha = false; // if get the exceptions

        try
        {
            $service->commit();
        }
        catch(InvalidException $e)
        {
            $gotcha = true;
        }
        $this->assertTrue($gotcha);
        $this->assertFalse($service->getErrors()->isEmpty());
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

    protected function getAuthCheckMock()
    {
        return m::mock('StdClass')
            ->shouldReceive('check')
            ->andReturn(true)
            ->getMock();
    }

    /**
     * @return mixed
     */
    private function getOrderSession()
    {
        $order_session = Session::get($this->order_service->getSessionKey());
        return $order_session;
    }

    private function MockAuthenticatorToReturnUserWithId($id)
    {
        $user_stub     = new User();
        $user_stub->id = $id;
        $mock_auth     = m::mock('StdClass')->shouldReceive('getLoggedUser')->andReturn($user_stub)->shouldReceive('getLoggedUserProfile')->andReturn([])->getMock();
        App::instance('authenticator', $mock_auth);
    }

}

class OrderServiceStub extends OrderService
{
    public function __construct($order = null)
    {
        parent::__construct();
        if($order) $this->order = $order;
    }

    private function getLoggedUserProfile()
    {
        return [];
    }
}