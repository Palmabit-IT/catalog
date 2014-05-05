<?php namespace Palmabit\Catalog\Tests;
use App;
use Palmabit\Catalog\Helpers\PathHierarchy;
/**
 * Test PathHierarchyTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class PathHierarchyTest extends DbTestCase {

    protected $path_hieararchy_helper;

    protected $category_top;
    protected $category_bottom;
    protected $product;

    /**
     * test
     * @runInSeparateProcess
     **/
    public function it_gets_tags_hierarchy_as_array()
    {
        $this->createCategoryHierarchy();
        $category_id = 2;
        $this->createProductAndAttachCategory($category_id);

        $this->path_hieararchy_helper = new PathHierarchy();

        $node_array = $this->path_hieararchy_helper->getHierarchyPathArray($this->product);

        $expected_nodes = [$this->category_top, $this->category_bottom, $this->product];

        // compare every category
        $this->assertEquals($expected_nodes[0]->toArray(),$node_array[0]->toArray());
        $this->assertEquals($expected_nodes[1]->description,$node_array[1]->description);
        $this->assertEquals($expected_nodes[2]->description,$node_array[2]->description);
    }

    protected function createCategoryHierarchy()
    {
        $category_repository = App::make('category_repository');
        $this->category_top             = $category_repository->create(array("description" => "1", "slug" => "1", "slug_lang" => "1"));
        $this->category_bottom          = $category_repository->create(array("description" => "2", "slug" => "2", "slug_lang" => "2"));
        $category_repository->setParent(2, 1);
    }

    protected function createProductAndAttachCategory($category_id)
    {
        $product_repository = App::make('product_repository');
        $product_input      = [
            "description" => "product description", "code" => "code", "name" => "name", "slug" => "slug1", "slug_lang" => "", "long_description" => "", "featured" => 1, "public" => 1, "offer" => 1, "stock" => 1, "with_vat" => 1, "video_link" => "", "professional" => 1, "price1" => "12.22", "price2" => "8.21", "price3" => "5.12", "price4" => "2.12", "quantity_pricing_enabled" => 0, "quantity_pricing_quantity" => 100,];
        $this->product = $product_repository->create($product_input);
        $product_repository->associateCategory($this->product->id, $category_id);
    }

}
 