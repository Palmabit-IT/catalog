<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Repository\EloquentProductImageRepository;
use Palmabit\Catalog\Repository\EloquentProductRepository;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Models\Category;
use Mockery as m;
use App;

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
        $this->assertEquals(2, $obj->slug_lang);
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
            "long_description" => "",
            "featured" => 1,
            "public" => 1,
            "offer" => 1,
            "stock" => 1,
            "with_vat" => 1,
            "video_link" => "http://www.google.com/video/12312422313",
            "professional" => 1,
            "price1" => "12.22",
            "price2" => "8.21",
            "price3" => "5.12",
            "price4" => "2.12",
            "quantity_pricing_enabled" => 0,
            "quantity_pricing_quantity" => 100,
        ];
        $obj = $this->r->create($data);
        $this->assertTrue($obj instanceof Product);
        $this->assertEquals($description, $obj->description);
        $this->assertEquals(1, $obj->public);
        $this->assertEquals(1, $obj->offer);
        $this->assertEquals(true, $obj->stock);
        $this->assertEquals(1, $obj->with_vat);
        $this->assertEquals("http://www.google.com/video/12312422313", $obj->video_link);
        $this->assertEquals(1, $obj->professional);
        $this->assertEquals("12.22", $obj->price1);
        $this->assertEquals("8.21", $obj->price2);
        $this->assertEquals("5.12", $obj->price3);
        $this->assertEquals("2.12", $obj->price4);
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
     * @test
     * @group p
     **/
    public function it_returns_associated_products()
    {
        $mock_get = m::mock('StdClass')->shouldReceive('get')->once()->andReturn(["first","second"])->getMock();
        $mock_model = m::mock('StdClass')->shouldReceive('accessories')->once()->andReturn($mock_get)->getMock();
        $mock_repo = m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->makePartial()->shouldReceive('find')->once()->andReturn($mock_model)->getMock();
        $repo = $mock_repo;

        $acc = $repo->getAccessories(1);
        $this->assertEquals(2, count($acc));
    }
    
    /**
     * @test
     **/
    public function it_gets_n_products_prediliging_offers()
    {
        $this->prepareFakeData();
        $this->r->update(1, ["offer"=>1]);

        $products = $this->r->getFirstOffersMax(4);

        $this->assertEquals(4, count($products));
        $this->assertEquals(1, $products[0]->offer);
    }

    /**
     * @test
     * @group duplicate
     **/
    public function it_duplicates_products_cats_imgs_accessories_and_create_unique_slug_lang()
    {
        // prepare product with related classes
        $this->prepareFakeData(2);
        $this->r->attachProduct(1,2);
        Category::create([
                         "description"=> "descrizione",
                         "slug" => "slug",
                         "slug_lang" => "slug",
                         "lang" => "it"
                         ]);
        $this->r->associateCategory(1,1);

        $mock_img_repo = new ImgRepoStub;
        $img_data = [
            "description" => "desc",
            "product_id" => 1,
            "featured" => 0,
            "data" => 111
        ];
        $mock_img_repo->create($img_data);

        $prod = $this->r->duplicate(1);

        // product duplicated
        $prod3 = $this->r->find(3);
        $prod1 = $this->r->find(1);
        // check that name contains copy
        $expected_name = $prod1->name . "_copia";
        $this->assertEquals($prod3->name, $expected_name );
        // check that i return a cloned product with no slug lang
        $this->assertEquals($prod->id,$prod->slug_lang);
        // categories
        $cat_original = $prod3->categories()->get()->lists('id');
        $cat_associated = $prod3->categories()->get()->lists('id');
        $this->assertEquals($cat_original, $cat_associated);
        // accessories
        $acc_original = $prod3->accessories()->get()->lists('id');
        $acc_associated = $prod3->accessories()->get()->lists('id');
        $this->assertEquals($acc_original, $acc_associated);
        // images
        $image_original = App::make('product_image_repository')->getByProductId($prod1->id);
        $image_associated = App::make('product_image_repository')->getByProductId($prod3->id);
        $this->assertEquals(count($image_original), count($image_associated));
        $this->assertEquals($image_original->first()->data, $image_associated->first()->data);

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

class ImgRepoStub extends EloquentProductImageRepository
{
    public function getBinaryData()
    {
        return 1;
    }
}