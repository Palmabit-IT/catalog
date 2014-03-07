<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Repository\EloquentCategoryRepository;
use Palmabit\Catalog\Models\Category;

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
     **/
//    public function it_associate_a_parent_node()
//    {
//        $cat1 = $this->repo->create(array("description"=> "1", "slug" => "1", "slug_lang" => "slug") );
//        $cat2 = $this->repo->create(array("description"=> "2", "slug" => "2", "slug_lang" => "slug") );
//        $this->repo->setParent($cat1->id, $cat2->id);
//
//        $cat1 = $this->repo->find(1);
//        $this->assertEquals($cat2->id, $cat1->parent_id);
//    }
}

class RepoStubLang extends EloquentCategoryRepository
{
    public function getLang()
    {
        return 'it';
    }

}