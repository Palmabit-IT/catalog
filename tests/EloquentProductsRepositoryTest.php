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
            "stock" => 4,
            "with_vat" => 1,
            "video_link" => "http://www.google.com/video/12312422313"
        ];
        $obj = $this->r->create($data);
        $this->assertTrue($obj instanceof Product);
        $this->assertEquals($description, $obj->description);
        $this->assertEquals(1, $obj->public);
        $this->assertEquals(1, $obj->offer);
        $this->assertEquals(4, $obj->stock);
        $this->assertEquals(1, $obj->with_vat);
        $this->assertEquals("http://www.google.com/video/12312422313", $obj->video_link);
    }

    /**
     * @test
     **/
    public function it_attach_another_product_as_accessory()
    {
        $this->prepareFakeData(2);

        $this->r->attachProduct(1,2);
        $product1 = $this->r->find(1);
        $number_of_accessories = $product1->accessories()->count();

        $this->assertEquals(1, $number_of_accessories);
    }

    /**
     * @test
     **/
    public function it_detach_another_product_as_accessory()
    {
        $this->prepareFakeData(2);

        $this->r->attachProduct(1,2);
        $product1 = $this->r->find(1);

        $this->r->detachProduct(1,2);
        $number_of_accessories = $product1->accessories()->count();

        $this->assertEquals(0, $number_of_accessories);
    }

    /**
     * @test
     **/
    public function it_associate_multiple_categories()
    {
        $this->prepareFakeData(1);
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
        $this->prepareFakeData(1);
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
        $this->prepareFakeData(1);
        $this->r->deassociateCategory(2,1);
    }

    /**
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     */
    public function testAssociateCategoryThrowsModelNotFoundException()
    {
        $this->r->associateCategory(1,2);
    }

    /**
     * Creates n random products
     * @param $number
     */
    protected function prepareFakeData($number = 5)
    {
        $faker = $this->faker;

        foreach(range(1,$number) as $key)
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