<?php  namespace Palmabit\Catalog\Tests;
use Palmabit\Catalog\Repository\EloquentOrderRepository;

/**
 * Test EloquentOrderRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class EloquentOrderRepositoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function is_initialized_with_order_model()
    {
        $repo = new EloquentOrderRepository();
        $this->assertEquals('Palmabit\Catalog\Models\Order', get_class($repo->getModel() ) );
    }

}
 