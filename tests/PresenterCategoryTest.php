<?php  namespace Palmabit\Catalog\Tests; 

/**
 * Test PresenterCategoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\ProductImage;
use Palmabit\Catalog\Presenters\PresenterCategory;

class PresenterCategoryTest extends \PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @test
     **/
    public function it_gets_featured_image()
    {
        $presenter = m::mock('Palmabit\Catalog\Presenters\PresenterCategory')->makePartial()->shouldReceive('image')->once()->andReturn(22)->getMock();
        $this->assertEquals(22, $presenter->featured_image);
    }

    /**
     * @test
     **/
    public function it_returns_the_description_and_name()
    {
        $category = new Category([
                               "description" => "desc",
                               "name" => "name",
                               "slug" => "slug",
                               "slug_lang" => "",
                               ]);
        $presenter = new PresenterCategory($category);
        $this->assertEquals("name", $presenter->name);
        $this->assertEquals("desc", $presenter->description);
    }

    /**
     * @test
     **/
    public function it_gets_siblings_with_same_lang()
    {
        $get_mock = m::mock('StdClass')->shouldReceive('get')->andReturn(1)->getMock();
        $mock_where = m::mock('StdClass')->shouldReceive('whereLang')->andReturn($get_mock)->getMock();
        $mock_resource = m::mock('StdClass')->shouldReceive('siblings')->andReturn($mock_where)->getMock();
        $presenter = new PresenterCategory($mock_resource);

        $this->assertEquals(1, $presenter->siblings());
    }
}
 