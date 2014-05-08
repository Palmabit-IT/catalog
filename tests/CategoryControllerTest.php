<?php  namespace Palmabit\Catalog\Tests; 
use App;
/**
 * Test CategoryControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CategoryControllerTest extends DbTestCase {

    protected $category_repository;

    public function setUp()
    {
        parent::setUp();
        $this->category_repository = App::make('category_repository');
    }

    /**
     * @test
     **/
    public function canChangeProductOrders()
    {
        $order = 1;
        $this->createACategoryWithOrder($order);

        $new_order = 3;
        $this->action('POST', 'Palmabit\Catalog\Controllers\CategoryController@postChangeOrder',['id' => 1, 'order' => $new_order]);

        $cat = $this->category_repository->all();
        $this->assertEquals($cat[0]->order, $new_order);

        $this->assertRedirectedToAction("Palmabit\\Catalog\\Controllers\\CategoryController@lists");
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function itchangeProductsOrderWithError()
    {
        $this->action('POST', 'Palmabit\Catalog\Controllers\CategoryController@postChangeOrder');

        $this->assertRedirectedToAction("Palmabit\\Catalog\\Controllers\\CategoryController@lists");
        $this->assertSessionHas('errors');
    }

    protected function createACategoryWithOrder($order)
    {
        $cat_values = [
            "description" => "description2", "slug" => "slug1", "slug_lang" => "slug2",
            "lang"        => 'it', 'order' => $order
        ];
        $this->category_repository->create($cat_values);
    }

}
 