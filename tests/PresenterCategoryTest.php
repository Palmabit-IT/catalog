<?php  namespace Palmabit\Catalog\Tests; 

/**
 * Test PresenterCategoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use Palmabit\Catalog\Models\Category;
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
}
 