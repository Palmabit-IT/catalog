<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Repository\EloquentCategoryRepository;
use Palmabit\Catalog\Models\Category;
use Event;

class EloquentCategoryRepositoryTest extends DbTestCase {

    protected $faker;
    protected $repo;

    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
        $this->repo = new RepoStubLang();

    }

    public function testCreate()
    {
        $desc= "description";
        $cat = $this->repo->create(array("description"=> $desc, "slug" => "slug", "slug_lang" => "slug") );
        $this->assertTrue($cat instanceof Category);
        $this->assertEquals($desc, $cat->description);
    }

    public function testSearch()
    {
        $description = "description";
        Category::create(array("description"=>$description , "slug" => "slug", "slug_lang" => "slug"));

        $cat = $this->repo->search($description);
        $this->assertNotEmpty($cat);

        $cat = $this->repo->search("not found");
        $this->assertEmpty($cat);
    }

    public function testUpdateWorks()
    {
        $desc= "description";
        $cat = $this->repo->create(array("description"=> $desc, "slug" => "slug", "slug_lang" => "slug") );
        $id =$cat->id;

        $newdesc= "new descriptin";
        $cat = $this->repo->update($id,array("description"=> $newdesc, "slug" => "slug") );

        $this->assertEquals($newdesc, $cat->description);
    }

    public function testDeleteWorks()
    {
        $desc= "description";
        $cat = $this->repo->create(array("description"=> $desc, "slug" => "slug", "slug_lang" => "slug") );

        $this->assertTrue( $this->repo->delete($cat->id) );
    }

    /**
     * @test
     **/
    public function it_gets_only_root_categories()
    {
        $this->repo->create(array("description"=> "", "slug" => "slug1", "slug_lang" => "slug") );

        $this->repo->create(array("description"=> "", "slug" => "slug2", "slug_lang" => "slug") );
        $results = $this->repo->getRootNodes();
        $this->assertEquals(2, count($results));
    }
    /**
     * @test
     * need to fix invalidattribute exception with multiple nodes
     **/
//    public function it_associate_a_parent_node()
//    {
//        $cat1 = $this->repo->create(array("description"=> "1", "slug" => "1", "slug_lang" => "slug1") );
//        $cat2 = $this->repo->create(array("description"=> "2", "slug" => "2", "slug_lang" => "slug2") );
//        $this->repo->setParent($cat1->id, $cat2->id);
//
//        $cat1 = $this->repo->find(1);
//        $this->assertEquals($cat2->id, $cat1->parent_id);
//    }

    /**
     * @test
     * @group all
     **/
    public function it_gets_all_products_order_by_depth_and_description()
    {
        $this->prepareCategoryHierarchy();

        $cats = $this->repo->all();

        //check the ordering depending on depth and description
        $this->assertEquals("slug1", $cats[0]->slug);
        $this->assertEquals("slug2", $cats[1]->slug);
        $this->assertEquals("slug3", $cats[2]->slug);
    }

    /**
     * @test
     * @group select
     */
    public function it_gets_select_items_for_category_in_a_given_language()
    {
        $cat_values = [
            "description" => "desc",
            "slug" => "slug",
            "slug_lang" => "slug",
            "lang" => 'it'
        ];
        $this->repo->create($cat_values);

        $expected_data = ["1" => "desc"];
        $data =  $this->repo->getArrSelectCat();

        $this->assertEquals($expected_data, $data);
    }

    protected function prepareCategoryHierarchy()
    {
        $cat_values = [
            "description" => "description2", "slug" => "slug2", "slug_lang" => "slug2", "lang" => 'it'];
        $this->repo->create($cat_values);
        $this->repo->setDepth(1,1);
        $cat_values = [
            "description" => "description4", "slug" => "slug1", "slug_lang" => "slug1", "lang" => 'it', "depth" => 0,];
        $this->repo->create($cat_values);
        $this->repo->setDepth(2,0);
        $cat_values = [
            "description" => "description3", "slug" => "slug3", "slug_lang" => "slug3", "lang" => 'it', "depth" => 1];
        $this->repo->create($cat_values);
        $this->repo->setDepth(3,1);
    }

    /**
     * @test
     **/
    public function it_set_cat_depth()
    {
        $cat_values = [
            "description" => "description2", "slug" => "slug2", "slug_lang" => "slug2", "lang" => 'it'];
        $this->repo->create($cat_values);
        $this->repo->setDepth(1,1);

        $cat_saved = $this->repo->find(1);

        $this->assertEquals(1, $cat_saved->depth);
    }
}

class RepoStubLang extends EloquentCategoryRepository
{
    public function getLang()
    {
        return 'it';
    }

}