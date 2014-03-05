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

    public function testDeleteWorkd()
    {
        $desc= "description";
        $cat = $this->repo->create(array("description"=> $desc, "slug" => "slug", "slug_lang" => "slug") );

        $this->assertTrue( $this->repo->delete($cat->id) );
    }
    
    /**
     * @test
     **/
    public function it_associate_a_parent_node()
    {
        $cat = $this->repo->create(array("description"=> $desc, "slug" => "slug", "slug_lang" => "slug") );
        $cat = $this->repo->create(array("description"=> $desc, "slug" => "slug", "slug_lang" => "slug") );

    }
}

class RepoStubLang extends EloquentCategoryRepository
{
    public function getLang()
    {
        return 'it';
    }

}