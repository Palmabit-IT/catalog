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
class PresenterCategoryProductFactoryTest extends \PHPUnit_Framework_TestCase {

    
    /**
     * @test
     * @group 1
     **/
    public function it_create_a_new_category_presenter_if_cats_has_childrens()
    {

        $cat = m::mock('Palmabit\Catalog\Models\Category')->shouldReceive('children')->once()->andReturn($mock_paginate)->getMock();
        $cat->prodotto = true;

        $mock_repo = m::mock('StdClass')->shouldReceive('hasChildrens')->once()->andReturn(true)->getMock();
        App::instance('category_repository', $mock_repo);

        $factory = new PresenterCategoryProductFactory();
        $presenter = $factory->create($cat);

        $this->assertInstanceOf('Palmabit\Library\Presenters\PresenterPagination', $presenter);
    }

}
 