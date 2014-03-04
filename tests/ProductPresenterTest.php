<?php namespace Palmabit\Catalog\Tests;
/**
 * Test PresenterProdottiTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Presenters\PresenterProducts;
use Mockery as m;

class ProductPresenterTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function it_gets_toggle_status_of_a_tab()
    {
        // test disabled
        $product = new Product();
        $presenter = new PresenterProducts($product);
        $disabled = $presenter->get_toggle;
        $this->assertEquals('data-toggle="" disabled="disabled"', $disabled);
        // test enabled
        $product->exists = true;
        $presenter = new PresenterProducts($product)
        ;
        $enabled= $presenter->get_toggle;
        $this->assertEquals('data-toggle="tab"', $enabled);
    }

    /**
     * @test
     **/
    public function it_gets_all_categories_of_a_product()
    {
        $arr_cats = [new Category, new Category];
        $mock_empty = m::mock('StdClass')
            ->shouldReceive('isEmpty')
            ->once()
            ->andReturn(false)
            ->shouldReceive('all')
            ->once()
            ->andReturn($arr_cats)
            ->getMock();
        $mock_get = m::mock('StdClass')
            ->shouldReceive('get')
            ->once()
            ->andReturn($mock_empty)
            ->getMock();
        $mock_product = m::mock('StdClass')
            ->shouldReceive('categories')->andReturn($mock_get)
            ->getMock();
        $presenter = new PresenterProducts($mock_product );
        $cats = $presenter->categories();

        $this->assertEquals(2, count($cats));
    }


}