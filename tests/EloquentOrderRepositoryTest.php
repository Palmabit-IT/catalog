<?php  namespace Palmabit\Catalog\Tests;
use Carbon\Carbon;
use Palmabit\Catalog\Repository\EloquentOrderRepository;
use Mockery as m;
use DB;
/**
 * Test EloquentOrderRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class EloquentOrderRepositoryTest extends \PHPUnit_Framework_TestCase {

    protected $order_repository;

    public function setUp()
    {
        $this->order_repository = new EloquentOrderRepository();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function is_initialized_with_order_model()
    {
        $repo = new EloquentOrderRepository();
        $this->assertEquals('Palmabit\Catalog\Models\Order', get_class($repo->getModel() ) );
    }

    /**
     * @test
     **/
    public function it_calculate_total_price()
    {
        $expected_amount = 10;
        $user_mock = m::mock('StdClass')
            ->shouldReceive('calculateTotalAmount')
            ->once()
            ->andReturn($expected_amount)
            ->getMock();

        $repo_mock = m::mock('Palmabit\Catalog\Repository\EloquentOrderRepository')
            ->makePartial()
            ->shouldReceive('find')
            ->once()
            ->andReturn($user_mock)
            ->getMock();


        $total_amount = $repo_mock->calculateTotalAmount(1);

        $this->assertEquals($expected_amount, $total_amount);
    }

    /**
     * @test
     **/
    public function it_get_order_by_order_id()
    {
        DB::table('order')->insert([
                                   "user_id" => 1,
                                   "date" => Carbon::now(),
                                   "completed" => 1,
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                   ]);
        $user_id = 1;
        $orders = $this->order_repository->getOrderByUserId($user_id);

        $this->assertCount(1, $orders);
    }

}
 