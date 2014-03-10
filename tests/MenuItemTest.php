<?php  namespace Palmabit\Catalog\Tests;
use Palmabit\Catalog\Menu\MenuItem;
use Mockery as m;
/**
 * Test MenuItemTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class MenuItemTest extends \PHPUnit_Framework_TestCase {
    public function tearDown()
    {
        m::close();
    }
    /**
     * @test
     **/
    public function it_can_create()
    {
        new MenuItem("name", "slug", "type");
    }

    /**
     * @test
     **/
    public function it_check_if_has_items()
    {
        $mock_collection = m::mock('Illuminate\Support\Collection')->shouldReceive('isEmpty')->once()->andReturn('false')->getMock();
        $item = new MenuItem("name", "slug", "type", $mock_collection);
        $this->assertFalse($item->hasItems());
    }

    /**
     * @test
     **/
    public function it_adds_items_to_collection()
    {
        $menu = new MenuItem("name", "slug", "type");

        $menu->add(["test"]);
        $menu->add(["test"], "key_test");

        $this->assertEquals(2, $menu->getCollection()->count());
        $this->assertEquals(["test"], $menu->getCollection()->get('key_test'));
    }
}
 