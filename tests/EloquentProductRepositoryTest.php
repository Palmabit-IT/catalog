<?php namespace Palmabit\Catalog\Tests;

use Illuminate\Database\Eloquent\Collection;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\ProductDescription;
use Palmabit\Catalog\Repository\EloquentProductImageRepository;
use Palmabit\Catalog\Repository\EloquentProductRepository;
use Palmabit\Catalog\Models\Product;
use Mockery as m;
use App, L;
use Palmabit\Catalog\Tests\Traits\ProductStubTrait;

class EloquentProductRepositoryTest extends DbTestCase
{
    use ProductStubTrait;

    protected $repository_stub;
    protected $faker;
    protected $default_lang;
    protected $current_lang;

    public function setUp()
    {
        parent::setUp();
        $this->current_lang = 'it';
        $this->default_lang = 'en';
        $this->repository_stub = new ProdRepoStubLang();
        $this->repository_stub->setLang($this->current_lang);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function canFindProductsFromSlug()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $product_description = $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product))->first();

        $product_found = $this->repository_stub->findBySlug($product_description->slug);
        $this->assertTrue($product_found->exists);
        $this->assertObjectHasAllAttributes($product->toArray(), $product_found, ['updated_at', 'created_at', 'deleted_at', 'type']);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlugThrowsModelNotFoundException()
    {
        $this->repository_stub->findBySlug($this->faker->unique()->text(20));
    }

    /**
     * @test
     */
    public function canGetFeaturedProducts()
    {
        $this->times(10)->make('Palmabit\Catalog\Models\Product', ["featured" => 1, "public" => 1]);
        $featured = $this->repository_stub->featuredProducts();
        $this->assertEquals(4, count($featured));
    }

    /**
     * @test
     */
    public function canGetOfferProducts()
    {
        $this->times(10)->make('Palmabit\Catalog\Models\Product', ["offer" => 1, "public" => 1]);
        $offers = $this->repository_stub->offerProducts();
        $this->assertEquals(4, count($offers));
    }

    /**
     * @test
     */
    public function canUpdateProductAndDescription()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product))->first();

        $update_data = [
                "description" => $this->faker->unique()->text(50),
                "code"        => $this->faker->unique()->lexify('??????')
        ];
        $product_updated = $this->repository_stub->update($product->id, $update_data);

        $this->assertObjectHasAllAttributes($update_data, $product_updated->decorateLanguage());
    }

    /**
     * @test
     **/
    public function itCanUpdateEmptyPrice()
    {
        $this->make('Palmabit\Catalog\Models\Product')->first();

        $product_updated = $this->repository_stub->update(1, [
                "price1" => "",
                "price2" => "",
                "price3" => "",
                "price4" => "",
        ]);
        $this->assertNull($product_updated->price1);
        $this->assertNull($product_updated->price2);
        $this->assertNull($product_updated->price3);
        $this->assertNull($product_updated->price4);
    }

    /**
     * @test
     **/
    public function canDeleteProductWithDescriptions()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product))->first();

        $this->repository_stub->delete(1);

        $this->assertEquals(0, Product::count());
        $this->assertEquals(0, ProductDescription::count(), 'The product still has description left');
    }

    public function testCreateWorks()
    {
        $product_data = $this->getModelStub();
        $product_description_data = $this->getProductDescriptionStub();
        unset($product_description_data["product_id"]);
        $data = array_merge($product_data, $product_description_data);

        $created_product = $this->repository_stub->create($data);
        $this->assertObjectHasAllAttributes($data, $created_product->decorateLanguage(), ['order']);
    }

    /**
     * @test
     **/
    public function it_attach_another_product_as_accessory()
    {
        $second_product_id = 2;
        $first_product_id = 1;
        $this->times(2)->make('Palmabit\Catalog\Models\Product')->first();

        $this->repository_stub->attachProduct($first_product_id, $second_product_id);

        $product1 = $this->repository_stub->find($first_product_id);
        $number_of_accessories = $product1->accessories()->count();

        $this->assertEquals($first_product_id, $number_of_accessories);
    }

    /**
     * @test
     **/
    public function it_detach_another_product_as_accessory()
    {
        $this->times(2)->make('Palmabit\Catalog\Models\Product')->first();

        $first_product_id = 1;
        $second_product_id = 2;
        $this->repository_stub->attachProduct($first_product_id, $second_product_id);
        $product1 = $this->repository_stub->find($first_product_id);

        $this->repository_stub->detachProduct($first_product_id, $second_product_id);
        $number_of_accessories = $product1->accessories()->count();

        $this->assertEquals(0, $number_of_accessories);
    }

    /**
     * @test
     **/
    public function it_associate_multiple_categories()
    {
        $this->make('Palmabit\Catalog\Models\Product')->first();

        foreach(range(1, 5) as $key)
        {
            Category::create([
                                     "name" => $this->faker->text(10),
                             ]);
        }

        $product = $this->repository_stub->find(1);
        $this->repository_stub->associateCategory($product->id, 1);
        $this->repository_stub->associateCategory($product->id, 2);
        $cats_number = $product->categories()->count();
        $this->assertEquals($cats_number, 2);
    }

    /**
     * @test
     **/
    public function it_deassociate_a_given_category()
    {
        $this->make('Palmabit\Catalog\Models\Product')->first();
        foreach(range(1, 2) as $key)
        {
            Category::create([
                                     "name" => $this->faker->text(10),
                             ]);
        }
        $product = $this->repository_stub->find(1);

        $product->categories()->attach(1);
        $this->repository_stub->deassociateCategory(1, 1);
        $this->assertEquals(0, $product->categories()->count());
    }

    /**
     * @test
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     **/
    public function it_throw_exception_if_try_to_deassociate_a_product_not_found()
    {
        $this->make('Palmabit\Catalog\Models\Product')->first();

        $this->repository_stub->deassociateCategory(2, 1);
    }

    /**
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     */
    public function testAssociateCategoryThrowsModelNotFoundException()
    {
        $this->repository_stub->associateCategory(1, 2);
    }

    /**
     * @test
     **/
    public function it_returns_associated_products()
    {
        $mock_get = m::mock('StdClass')->shouldReceive('get')->once()->andReturn(["first", "second"])->getMock();
        $mock_model = m::mock('StdClass')->shouldReceive('accessories')->once()->andReturn($mock_get)->getMock();
        $repo = $mock_repo = m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->makePartial()
                              ->shouldReceive('find')
                              ->once()
                              ->andReturn($mock_model)
                              ->getMock();

        $accessories = $repo->getAccessories(1);
        $this->assertEquals(2, count($accessories));
    }

    /**
     * @test
     **/
    public function it_gets_n_products_prediliging_offers()
    {
        $this->times(1)->make('Palmabit\Catalog\Models\Product', ["offer" => 0, "public" => 1])->first();
        $this->times(4)->make('Palmabit\Catalog\Models\Product', ["offer" => 1, "public" => 1])->first();

        $products = $this->repository_stub->getOnlyFirstOffersMax(4);

        $this->assertEquals(4, count($products));
    }

    /**
     * @test
     * @group duplicate
     **/
    public function it_duplicates_products_cats_imgs_accessories_and_create_unique_slug_lang()
    {
        // prepare product with related classes
        /*   $this->prepareFakeData(2);
           $this->r->attachProduct(1, 2);
           Category::create([
                                    "name" => "name",
                            ]);
           $this->r->associateCategory(1, 1);

           $mock_img_repo = new ImgRepoStub;
           $img_data = [
                   "description" => "desc",
                   "product_id"  => 1,
                   "featured"    => 0,
                   "data"        => 111
           ];
           $mock_img_repo->create($img_data);

           $prod = $this->r->duplicate(1);

           // product duplicated
           $prod3 = $this->r->find(3);
           $prod1 = $this->r->find(1);
           // check that name contains copy
           $expected_name = $prod1->name . "_copia";
           $this->assertEquals($prod3->name, $expected_name);
           // check that i return a cloned product with no slug lang
           $this->assertEquals($prod->id, $prod->slug_lang);
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
           $this->assertEquals($image_original->first()->data, $image_associated->first()->data);  */
    }

    /**
     * @test
     */
    public function canGetAllProducts()
    {
        $product_1 = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product_1));
        $product_2 = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product_2));

        $results_per_page = 5;
        \Config::set('catalog::admin_per_page', $results_per_page);

        $products = $this->repository_stub->all();
        $this->assertEquals($products->first()->id, $product_1->id);
        $this->assertEquals(2, count($products));
    }

    /**
     * @test
     **/
    public function it_gets_all_products_filtered_by_code_name_featured_public_offer_professional()
    {
        $product_1 = $this->make('Palmabit\Catalog\Models\Product', [
                "order"        => 1,
                "offer"        => 0,
                "public"       => 1,
                "professional" => 0,
                "featured"     => 1
        ])->first();
        $product_1_desc = $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product_1))->first();
        $product_2 = $this->make('Palmabit\Catalog\Models\Product', [
                "offer"        => 1,
                "public"       => 1,
                "professional" => 1,
                "featured"     => 1
        ])->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product_2));

        $product = $this->repository_stub->all(["code" => $product_1->code]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals("1", $product->first()->id);

        $product = $this->repository_stub->all(["name" => $product_1_desc->name]);
        $this->assertEquals($product_1_desc->name, $product->first()->name);

        $product = $this->repository_stub->all(["featured" => "1"]);
        $this->assertEquals(2, $product->count());
        $this->assertEquals(1, $product->first()->featured);

        $product = $this->repository_stub->all(["public" => "1"]);
        $this->assertEquals(2, $product->count());
        $this->assertEquals(1, $product->first()->public);

        $product = $this->repository_stub->all(["offer" => "1"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals(1, $product->first()->offer);

        $product = $this->repository_stub->all(["professional" => "1"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals(1, $product->first()->professional);
    }

    /**
     * @test
     **/
    public function it_gets_all_products_filtered_for_category()
    {
        $product_1 = $this->make('Palmabit\Catalog\Models\Product', [
                "order"        => 1,
                "offer"        => 0,
                "public"       => 1,
                "professional" => 0,
                "featured"     => 1
        ])->first();
        $product_1_desc = $this->make('Palmabit\Catalog\Models\ProductDescription', $this->getProductDescriptionStub($product_1))->first();

        $this->associateMultipleCategoryToProduct($product_1->id);

        $product = $this->repository_stub->all(["category_id" => 1]);
        $this->assertEquals($product_1->code, $product->first()->code);
    }

    /**
     * @test
     */
    public function itFindProductsByCodeAndLang()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();

        $product = $this->repository_stub->findByCodeAndLang($product->code);
        $this->assertNotNull($product);
    }

    /**
     * @ test
     *
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     **/
    public function itThrowExceptionIfCannotFindModel()
    {
        $this->repository_stub->findByCodeAndLang($this->faker->unique()->randomNumber(2));
    }

    /**
     * @test
     **/
    public function findAllLanguagesAvailableToAProduct_AsProductsCollection()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product')->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', array_merge($this->getProductDescriptionStub($product), ["lang" => "it"]))->first();
        $this->make('Palmabit\Catalog\Models\ProductDescription', array_merge($this->getProductDescriptionStub($product), ["lang" => "it"]))->first();

        $products_found = $this->repository_stub->getProductLangsAvailable($product->id);
        $this->assertCount(2, $products_found);
    }

    protected function prepareFakeSearchData()
    {
        $this->createProductsForSearch();

        $this->associateCategoryForSearch();
    }

    protected function createProductsForSearch()
    {
        Product::create([
                                "code"             => "1234",
                                "name"             => "name1",
                                "slug"             => "slug1",
                                "slug_lang"        => "slug_lang1",
                                "lang"             => 'it',
                                "description"      => "description",
                                "long_description" => "long_description",
                                "featured"         => true,
                                "public"           => true,
                                "offer"            => true,
                                "professional"     => true,
                                'order'            => 2
                        ]);
        Product::create([
                                "code"             => "1235",
                                "name"             => "name1",
                                "slug"             => "slug2",
                                "slug_lang"        => "slug_lang2",
                                "lang"             => 'it',
                                "description"      => "description",
                                "long_description" => "long_description",
                                "featured"         => false,
                                "public"           => false,
                                "offer"            => false,
                                "professional"     => false,
                                "order"            => 1
                        ]);
    }

    protected function associateCategoryForSearch()
    {
        App::make('category_repository')->create([
                                                         "name"      => "desc_cat_1",
                                                         "slug"      => "slug_desc_1",
                                                         "slug_lang" => "slug_1",
                                                         "lang"      => "it"
                                                 ]);
        App::make('product_repository')->associateCategory(1, 1);

        App::make('category_repository')->create([
                                                         "name"      => "desc_cat_2",
                                                         "slug"      => "slug_desc_2",
                                                         "slug_lang" => "slug_2",
                                                         "lang"      => "it"
                                                 ]);
        App::make('product_repository')->associateCategory(2, 2);
    }

    protected function associateMultipleCategoryToProduct($id)
    {
        App::make('category_repository')->create([
                                                         "name"      => "desc_cat_1",
                                                         "slug"      => "slug_desc_1",
                                                         "slug_lang" => "slug_1",
                                                         "lang"      => "it"
                                                 ]);
        App::make('product_repository')->associateCategory($id, 1);
        App::make('category_repository')->create([
                                                         "name"      => "desc_cat_1",
                                                         "slug"      => "slug_desc_2",
                                                         "slug_lang" => "slug_1",
                                                         "lang"      => "it"
                                                 ]);
        App::make('product_repository')->associateCategory($id, 2);
    }

    /**
     * @return mixed
     */
    protected function mockLanguageReturnDefaultLang()
    {
        return $this->mockLanguageReturnLang($this->default_lang);
    }
}

class ProdRepoStubLang extends EloquentProductRepository
{
    public static $current_lang = 'it';

    public function getLang()
    {
        return static::$current_lang;
    }

    public static function resetToDefaultLang()
    {
        static::$current_lang = 'it';
    }

    public static function setLang($lang)
    {
        static::$current_lang = $lang;
    }
}

class ImgRepoStub extends EloquentProductImageRepository
{
    public function getBinaryData()
    {
        return 1;
    }
}