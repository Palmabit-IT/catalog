<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Repository\EloquentProductImageRepository;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\ProductImage;

class EloquentProductImageRepositoryTest extends DbTestCase {

    protected $repo;
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
        $this->repo = new ImageRepositoryStub;
    }

    public function testCreateWorks()
    {
        $this->createProduct();
        $data = [
            "description" => "desc",
            "product_id" => 1,
            "featured" => 1,
            "image" => "1"
        ];
        $obj = $this->repo->create($data);
        $this->assertTrue(is_a($obj,'\Palmabit\Catalog\Models\ProductImage') );
        $this->assertEquals('desc', $obj->description);
    }

    public function testDeleteWorks()
    {
        $this->createProduct();
        $data = [
            "description" => "desc",
            "product_id" => 1,
            "featured" => 1,
            "image" => "",
        ];
        $obj = $this->repo->create($data);
        $this->repo->delete(1);
        $numero_cat = Category::all()->count();
        $this->assertEquals(0, $numero_cat);
    }

    public function testChangeFeaturedWorks()
    {
        $this->createProduct();
        // create cats
        $data = [
            "description" => "desc1",
            "product_id" => 1,
            "featured" => 1,
            "image" => ""
        ];
        $this->repo->create($data);
        $data = [
            "description" => "desc2",
            "product_id" => 1,
            "featured" => 0,
            "image" => ""
        ];
        $this->repo->create($data);
        // creazione prodotto
        $faker = $this->faker;
        Product::create([
                         "code" => $faker->text(5),
                         "name" => $faker->text(10),
                         "slug" => "slug_1",
                         "slug_lang" => "slug_lingua",
                         "lang" => 'it',
                         "description" => $faker->text(10),
                         "descrizione_long" => $faker->text(100),
                         "featured" => 1,
                         ]);

        $this->repo->changeFeatured(2,1);
        $img1 = ProductImage::find(1);
        $img2 = ProductImage::find(2);
        $this->assertEquals(0,$img1->featured);
        $this->assertEquals(1,$img2->featured);
    }

    protected function createProduct()
    {
        $description= "desc";
        $data = [
            "description" => $description,
            "code" => "code",
            "name" => "name",
            "slug" => "slug",
            "slug_lang" => "",
            "descrizione_long" => "",
            "in_evidenza" => 1,
        ];
        Product::create($data);
    }

    /**
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     */
    public function testChangeFeaturedThrowsNotFoundException()
    {
        $this->repo->changeFeatured(1,1);
    }

}

class ImageRepositoryStub extends EloquentProductImageRepository
{
    protected function getBinaryData()
    {
        return "123123121233";
    }
}