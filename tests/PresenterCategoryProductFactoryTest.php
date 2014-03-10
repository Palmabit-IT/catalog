<?php  namespace Palmabit\Catalog\Tests;
use Illuminate\Support\Facades\App;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Presenters\PresenterCategoryProductFactory;
use Mockery as m;
/**
 * Test PresenterCategoryProductFactoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class PresenterCategoryProductFactoryTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }
 
    /**
     * @test
     * need to fix makechild of bug with multiple test
     **/
    public function it_create_a_new_category_presenter_if_cats_has_childrens()
    {
//        $cat1 = Category::create(["description"=> "", "slug" => "slug1", "slug_lang" => "slug"]);
//        $cat2 = Category::create(["description"=> "", "slug" => "slug2", "slug_lang" => "slug"]);
//        $cat2->makeChildOf($cat1);
//
//        $mock_repo = m::mock('StdClass')->shouldReceive('hasChildrens')->once()->andReturn(true)->getMock();
//        App::instance('category_repository', $mock_repo);
//
//        $factory = new PresenterCategoryProductFactory();
//        $presenter = $factory->create($cat1);
//
//        $this->assertInstanceOf('Palmabit\Library\Presenters\PresenterPagination', $presenter);
    }
    
    /**
     * @test
     **/
    public function it_create_a_new_product_presenter_if_cats_has_no_childerns()
    {

    }

}
 