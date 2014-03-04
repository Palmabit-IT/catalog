<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Repository\EloquentProductRepository;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Models\Category;
use Mockery as m;

class EloquentProductsRepositoryTest extends DbTestCase {

    protected $r;
    protected $faker;


    public function setUp()
    {
        parent::setUp();

        $this->r = new ProdRepoStubLang();
        $this->faker = \Faker\Factory::create();
    }

    public function tearDown()
    {
        m::close();
    }

	public function testAllWorks()
	{
        $this->prepareFakeData();

        $app = m::mock('AppMock');
        $app->shouldReceive('instance')->once()->andReturn($app);

        \Illuminate\Support\Facades\Facade::setFacadeApplication($app);
        \Illuminate\Support\Facades\Config::swap($config = m::mock('ConfigMock'));

        $config->shouldReceive('get')->once()->andReturn(5);

        $objs = $this->r->all();
        $this->assertEquals(5, count($objs));
	}

    public function testFindBySlugWorks()
    {
        $this->prepareFakeData();
        $obj = $this->r->findBySlug(2);
        $this->assertTrue($obj->exists);
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testFindBySlugThrowsModelNotFoundException()
    {
       $this->r->findBySlug(1);
    }

    public function testFeaturedWorks()
    {
        $this->prepareFakeData();
        $objs = $this->r->featuredProducts();
        $this->assertEquals(1, count($objs));
    }

    public function testUpdateWorks()
    {
        $this->prepareFakeData();

        $obj = $this->r->update(2, ["lang" => "en"]);
        $this->assertEquals("en", $obj->lang);
    }

    public function testCreateWorks()
    {
        $description = "desc";
        $data = [
            "description" => $description,
            "code" => "code",
            "name" => "name",
            "slug" => "slug",
            "slug_lang" => "",
            "description_long" => "",
            "featured" => 1,
            "public" => 1,
            "offer" => 1,
        ];
        $obj = $this->r->create($data);
        $this->assertTrue($obj instanceof Product);
        $this->assertEquals($description, $obj->description);
        $this->assertEquals(1, $obj->public);
        $this->assertEquals(1, $obj->offer);
    }

//    public function testAssociateCategorySuccess()
//    {
//        $this->prepareFakeData();
//        foreach(range(1,5) as $key)
//        {
//            $cat = Category::create([
//                              "description" => $this->faker->text(10),
//                              "slug" => $this->faker->unique()->text(10),
//                              "lang" => "it",
//                              "slug_lang" => $this->faker->text(10),
//                              ]);
//            $cat->products()->attach([$cat->id]);
//        }
//
//        $product = $this->r->find(1);
//        $this->r->associateCategory($product->id, 3);
//        $cat_id = $product->categories()->first()->id;
//        $this->assertEquals(3,$cat_id);
//        $numero_cat = $product->categories()->count();
//        $this->assertEquals(1, $numero_cat);
//    }

    /**
     * @test
     **/
    public function it_associate_multiple_categories()
    {
        $this->prepareFakeData();
        foreach(range(1,5) as $key)
        {
            Category::create([
                              "description" => $this->faker->text(10),
                              "slug" => $this->faker->unique()->text(10),
                              "lang" => "it",
                              "slug_lang" => $this->faker->text(10),
                              ]);
        }

        $product = $this->r->find(1);
        $this->r->associateCategory($product->id, 1);
        $this->r->associateCategory($product->id, 2);
        $cats_number = $product->categories()->count();
        $this->assertEquals($cats_number , 2);
    }

    /**
     * @test
     **/
    public function it_deassociate_a_given_category()
    {
        $this->prepareFakeData();
        foreach(range(1,2) as $key)
        {
            Category::create([
                             "description" => $this->faker->text(10),
                             "slug" => $this->faker->unique()->text(10),
                             "lang" => "it",
                             "slug_lang" => $this->faker->text(10),
                             ]);
        }
        $product = $this->r->find(1);
        $product->categories()->attach(1);
        $this->r->deassociateCategory(1,1);
        $this->assertEquals(0, $product->categories()->count());
    }
    
    /**
     * @test
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     **/
    public function it_throw_exception_if_try_to_deassociate_a_product_not_found()
    {
        $this->prepareFakeData();
        $this->r->deassociateCategory(6,1);
    }

    /**
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     */
    public function testAssociateCategoryThrowsModelNotFoundException()
    {
        $this->r->associateCategory(1,2);
    }


    protected function prepareFakeData()
    {
        $faker = $this->faker;

        foreach(range(1,5) as $key)
        {
            Product::create([
                             "code" => $faker->text(5),
                             "name" => $faker->text(10),
                             "slug" => $key,
                             "slug_lang" => $key,
                             "lang" => 'it',
                             "description" => $faker->text(10),
                             "description_long" => $faker->text(100),
                             "featured" => $key == 5 ? true : false,
                             "public" => rand(0,1),
                             "offer" => 0
                             ]);
        }
    }



}

class ProdRepoStubLang extends EloquentProductRepository
{
    public function getLang()
    {
        return 'it';
    }

}