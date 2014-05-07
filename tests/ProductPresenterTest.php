<?php namespace Palmabit\Catalog\Tests;
/**
 * Test PresenterProdottiTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Support\Facades\App;
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

    /**
     * @test
     **/
    public function it_gets_products_products()
    {
        $arr_cats = [new Product, new Product];
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
            ->shouldReceive('accessories')->andReturn($mock_get)
            ->getMock();
        $presenter = new PresenterProducts($mock_product );
        $cats = $presenter->accessories();

        $this->assertEquals(2, count($cats));
    }
    
    /**
     * @test
     **/
    public function it_returns_the_featured_image()
    {
        $presenter = m::mock('Palmabit\Catalog\Presenters\PresenterProducts')->makePartial()->shouldReceive('features')->once()->andReturn(22)->getMock();
        $this->assertEquals(22, $presenter->featured_image);

    }

    /**
     * @test
     **/
    public function it_returns_the_description_and_name()
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
                               ]);
        $presenter = new PresenterProducts($product);
        $this->assertEquals("name", $presenter->name);
        $this->assertEquals("desc", $presenter->description);
    }
    
    /**
     * @test
     **/
    public function it_show_IfCanBeBoughtByTheCurrentUser()
    {
        $authenticator = m::mock('StdClass')
            ->shouldReceive("check")
            ->once()
            ->andReturn(true)
            ->shouldReceive('hasGroup')
            ->andReturn(true)
            ->getMock();
        App::instance('authenticator', $authenticator);

        $presenter = new PresenterProducts([]);
        $can_buy = $presenter->canBeBought();

        $this->assertTrue($can_buy);
    }
}