<?php namespace Palmabit\Catalog\Tests;

use Illuminate\Database\Eloquent\Collection;
use Palmabit\Catalog\Repository\EloquentProductImageRepository;
use Palmabit\Catalog\Repository\EloquentProductRepository;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Models\Category;
use Mockery as m;
use App, L;

class EloquentProductRepositoryTest extends DbTestCase
{

    protected $r;
    protected $faker;
    protected $default_lang;

    public function setUp()
    {
        parent::setUp();
        $this->default_lang = 'it';
        $this->r = new ProdRepoStubLang();
        $this->faker = \Faker\Factory::create();
    }

    public function tearDown()
    {
        m::close();
        ProdRepoStubLang::resetToDefaultLang();
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

    /**
     * @test
     **/
    public function itCanUpdateEmptyPrice()
    {
        $this->prepareFakeData();

        $obj = $this->r->update(2, [
                "price1" => "",
                "price2" => "",
                "price3" => "",
                "price4" => "",
        ]);
        $this->assertNull($obj->price1);
        $this->assertNull($obj->price2);
        $this->assertNull($obj->price3);
        $this->assertNull($obj->price4);
    }

    /**
     * @test
     **/
    public function it_doestNotUpdateSlugLang_IfAlreadyExistsOneWithTheSameLanguage()
    {
        $faker = $this->faker;
        $first_product_slug_lang = 1;
        Product::create([
                                "code"             => $faker->text(5),
                                "name"             => $faker->text(10),
                                "slug"             => "slug",
                                "slug_lang"        => $first_product_slug_lang,
                                "lang"             => 'it',
                                "description"      => $faker->text(10),
                                "description_long" => $faker->text(100),
                                "featured"         => 1,
                                "public"           => 1,
                                "offer"            => 0
                        ]);

        $second_product_slug_lang = 25;
        Product::create([
                                "code"             => $faker->text(5),
                                "name"             => $faker->text(10),
                                "slug"             => "",
                                "slug_lang"        => $second_product_slug_lang,
                                "lang"             => 'it',
                                "description"      => $faker->text(10),
                                "description_long" => $faker->text(100),
                                "featured"         => 1,
                                "public"           => 1,
                                "offer"            => 0
                        ]);

        $second_product_id = 2;
        $second_product = $this->r->update($second_product_id, ["slug" => $first_product_slug_lang, "slug_lang" => ""]);

        $this->assertEquals($second_product_slug_lang, $second_product->slug_lang);
    }

    public function testCreateWorks()
    {
        $description = "desc";
        $data = [
                "description"               => $description,
                "code"                      => "code",
                "name"                      => "name",
                "slug"                      => "slug",
                "slug_lang"                 => "",
                "long_description"          => "",
                "featured"                  => 1,
                "public"                    => 1,
                "offer"                     => 1,
                "stock"                     => 1,
                "with_vat"                  => 1,
                "video_link"                => "http://www.google.com/video/12312422313",
                "professional"              => 1,
                "price1"                    => "12.22",
                "price2"                    => "8.21",
                "price3"                    => "5.12",
                "price4"                    => "2.12",
                "quantity_pricing_enabled"  => 0,
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

        $this->r->attachProduct(1, 2);
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

        $this->r->attachProduct(1, 2);
        $product1 = $this->r->find(1);

        $this->r->detachProduct(1, 2);
        $number_of_accessories = $product1->accessories()->count();

        $this->assertEquals(0, $number_of_accessories);
    }

    /**
     * @test
     **/
    public function it_associate_multiple_categories()
    {
        $this->prepareFakeData(1);
        foreach(range(1, 5) as $key)
        {
            Category::create([
                                     "description" => $this->faker->text(10),
                                     "slug"        => $this->faker->unique()->text(10),
                                     "lang"        => "it",
                                     "slug_lang"   => $this->faker->text(10),
                             ]);
        }

        $product = $this->r->find(1);
        $this->r->associateCategory($product->id, 1);
        $this->r->associateCategory($product->id, 2);
        $cats_number = $product->categories()->count();
        $this->assertEquals($cats_number, 2);
    }

    /**
     * @test
     **/
    public function it_deassociate_a_given_category()
    {
        $this->prepareFakeData(1);
        foreach(range(1, 2) as $key)
        {
            Category::create([
                                     "description" => $this->faker->text(10),
                                     "slug"        => $this->faker->unique()->text(10),
                                     "lang"        => "it",
                                     "slug_lang"   => $this->faker->text(10),
                             ]);
        }
        $product = $this->r->find(1);
        $product->categories()->attach(1);
        $this->r->deassociateCategory(1, 1);
        $this->assertEquals(0, $product->categories()->count());
    }

    /**
     * @test
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     **/
    public function it_throw_exception_if_try_to_deassociate_a_product_not_found()
    {
        $this->prepareFakeData(1);
        $this->r->deassociateCategory(2, 1);
    }

    /**
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     */
    public function testAssociateCategoryThrowsModelNotFoundException()
    {
        $this->r->associateCategory(1, 2);
    }

    /**
     * @test
     * @group p
     **/
    public function it_returns_associated_products()
    {
        $mock_get = m::mock('StdClass')->shouldReceive('get')->once()->andReturn(["first", "second"])->getMock();
        $mock_model = m::mock('StdClass')->shouldReceive('accessories')->once()->andReturn($mock_get)->getMock();
        $mock_repo =
                m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->makePartial()->shouldReceive('find')->once()->andReturn($mock_model)
                 ->getMock();
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
        $this->r->update(1, ["offer" => 1]);

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
        $this->r->attachProduct(1, 2);
        Category::create([
                                 "description" => "descrizione",
                                 "slug"        => "slug",
                                 "slug_lang"   => "slug",
                                 "lang"        => "it"
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
        $this->assertEquals($image_original->first()->data, $image_associated->first()->data);
    }

    /**
     * @test
     **/
    public function it_gets_all_products_filtered_by_code_name_featured_public_offer_professional()
    {
        $this->prepareFakeSearchData();

        $product = $this->r->all(["code" => "1234"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals("name1", $product->first()->name);
        $this->assertEquals("1", $product->first()->id);

        $product = $this->r->all(["name" => "name1"]);
        $this->assertEquals(2, $product->count());
        $this->assertEquals("name1", $product->first()->name);

        $product = $this->r->all(["featured" => "1"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals(1, $product->first()->featured);

        $product = $this->r->all(["public" => "1"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals(1, $product->first()->public);

        $product = $this->r->all(["offer" => "1"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals(1, $product->first()->offer);

        $product = $this->r->all(["professional" => "1"]);
        $this->assertEquals(1, $product->count());
        $this->assertEquals(1, $product->first()->professional);
    }

    /**
     * @test
     **/
    public function it_gets_all_products_ordered_by_category_getting_only_one_row_per_product()
    {
        $this->createProductsForSearch();
        $id = 1;
        $this->associateMultipleCategoryToProduct($id);

        $product = $this->r->all([]);
        $this->assertEquals(2, $product->count());
    }

    /**
     * @test
     **/
    public function it_gets_all_products_filtered_for_category()
    {
        $this->createProductsForSearch();
        $id = 1;
        $this->associateMultipleCategoryToProduct($id);

        $product = $this->r->all(["category_id" => 1]);
        $this->assertEquals("1234", $product->first()->code);
    }

    /**
     * @test
     **/
    public function it_filter_producs_with_all_ignoring_empty_query_strings_and_order_by_categorydescription_order_and_name()
    {
        $this->prepareFakeSearchData();

        //@todo check that data is ordered by cat and show it in the view
        $product = $this->r->all(["code" => ""]);
        $this->assertEquals(2, $product->count());
        // check that ordering by cat is the most important
        $this->assertEquals("slug1", $product->first()->slug);
        $this->assertEquals(2, $product->first()->order);
    }

    /**
     * @test
     **/
    public function it_findProductBySlugLang()
    {
        $slug_lang = "slug lang";
        $product_expected = Product::create([
                                                    "code"             => "1234",
                                                    "name"             => "product name",
                                                    "slug"             => "slug",
                                                    "slug_lang"        => $slug_lang,
                                                    "lang"             => 'it',
                                                    "description"      => "product description",
                                                    "long_description" => "product long description",
                                                    "featured"         => 1,
                                                    "public"           => 0,
                                                    "offer"            => 0
                                            ]);
        $product = $this->r->findBySlugLang($slug_lang);

        $this->assertContains($product_expected->code, $product->code);
    }

    /**
     * @test
     */
    public function itFindProductsByCodeAndLang()
    {
        $lang = "it";
        $code = "1";
        $attributes = [
                "code"             => $code,
                "name"             => "name",
                "slug"             => "",
                "slug_lang"        => "",
                "lang"             => $lang,
                "description"      => "",
                "long_description" => "",
                "featured"         => 0,
                "public"           => 1,
                "offer"            => 0
        ];
        Product::create($attributes);

        $product = $this->r->findByCodeAndLang($code, $lang);
        $this->objectHasAllArrayAttributes($attributes, $product);
    }

    /**
     * @test
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     **/
    public function itThrowExceptionIfCannotFindModel()
    {
        $product = $this->r->findByCodeAndLang("1", "id");
    }

    /**
     * @test
     **/
    public function findAllLanguagesAvailableToAProduct_AsProductsCollection()
    {
        $this->resetTimes();
        $product_it = $this->make('Palmabit\Catalog\Models\Product', [
                "lang"      => 'it',
                "slug"      => 'slug',
                "slug_lang" => 'slug'
        ]);
        $product_en = $this->make('Palmabit\Catalog\Models\Product', [
                "lang"      => 'en',
                "slug"      => 'slug',
                "slug_lang" => 'slug'
        ]);

        $expected_products = new Collection([$product_en[0], $product_it[0]]);

        $products_found = $this->r->getProductLangsAvailable('slug');
        $this->assertEquals($products_found->lists('id'), $expected_products->lists('id'));
    }

    protected function getModelStub()
    {
        return [
                "code"             => $this->faker->unique()->text(5),
                "name"             => $this->faker->unique()->text(10),
                "slug"             => $this->faker->unique()->text(5),
                "slug_lang"        => $this->faker->unique()->text(10),
                "lang"             => 'it',
                "description"      => $this->faker->unique()->text(10),
                "long_description" => $this->faker->unique()->text(100),
                "featured"         => (integer)$this->faker->boolean(50),
                "public"           => 1,
                "offer"            => 0
        ];
    }

    /**
     * Creates n random products
     *
     * @param $number
     */
    protected function prepareFakeData($number = 5)
    {
        $faker = $this->faker;

        foreach(range(1, $number) as $key)
        {
            Product::create([
                                    "code"             => $faker->text(5),
                                    "name"             => $faker->text(10),
                                    "slug"             => $key,
                                    "slug_lang"        => $key,
                                    "lang"             => 'it',
                                    "description"      => $faker->text(10),
                                    "long_description" => $faker->text(100),
                                    "featured"         => $key == 5 ? true : false,
                                    "public"           => 1,
                                    "offer"            => 0
                            ]);
        }
    }

    protected function prepareFakeSearchData()
    {
        $this->createProductsForSearch();

        $this->associateCategoryForSearch();
    }

    protected function createProductsForSearch()
    {
        Product::create([
                                "code"        => "1234", "name" => "name1", "slug" => "slug1", "slug_lang" => "slug_lang1", "lang" => 'it',
                                "description" => "description", "long_description" => "long_description", "featured" => true, "public" => true,
                                "offer"       => true, "professional" => true, 'order' => 2]);
        Product::create([
                                "code"        => "1235", "name" => "name1", "slug" => "slug2", "slug_lang" => "slug_lang2", "lang" => 'it',
                                "description" => "description", "long_description" => "long_description", "featured" => false, "public" => false,
                                "offer"       => false, "professional" => false, "order" => 1]);
    }

    protected function associateCategoryForSearch()
    {
        App::make('category_repository')->create([
                                                         "description" => "desc_cat_1", "slug" => "slug_desc_1", "slug_lang" => "slug_1",
                                                         "lang"        => "it",]);
        App::make('product_repository')->associateCategory(1, 1);

        App::make('category_repository')->create([
                                                         "description" => "desc_cat_2", "slug" => "slug_desc_2", "slug_lang" => "slug_2",
                                                         "lang"        => "it",]);
        App::make('product_repository')->associateCategory(2, 2);
    }

    protected function associateMultipleCategoryToProduct($id)
    {
        App::make('category_repository')->create([
                                                         "description" => "desc_cat_1", "slug" => "slug_desc_1", "slug_lang" => "slug_1",
                                                         "lang"        => "it",]);
        App::make('product_repository')->associateCategory($id, 1);
        App::make('category_repository')->create([
                                                         "description" => "desc_cat_1", "slug" => "slug_desc_2", "slug_lang" => "slug_1",
                                                         "lang"        => "it",]);
        App::make('product_repository')->associateCategory($id, 2);
    }

    /**
     * @test
     **/
    public function canActivateSetGeneralFormFilter()
    {
        $mock_product_filter = $this->mockSetGeneralFormFilter(true);

        $product_repo = new ProdRepoStubLang(false, $mock_product_filter);
        $repo_obtained = $product_repo->enableGeneralFormFilter();

        $this->assertSame($repo_obtained, $product_repo);
        $this->assertTrue($repo_obtained->general_form_filter);
    }

    /**
     * @test
     **/
    public function canDeactivateGeneralFormFilterEnabled()
    {
        $mock_product_filter = $this->mockSetGeneralFormFilter(false);

        $product_repo = new ProdRepoStubLang(false, $mock_product_filter);
        $repo_obtained = $product_repo->disableGeneralFormFilter();

        $this->assertSame($repo_obtained, $product_repo);
        $this->assertFalse($repo_obtained->general_form_filter);
    }

    /**
     * @test
     **/
    public function canUpdateOnlyGeneralInOtherLanguagesDataIfEnabled()
    {
        $product = $this->make('Palmabit\Catalog\Models\Product', $this->getModelStub())->first();
        $update_data = [
                "code"             => $this->faker->unique()->text(5),
                "name"             => $this->faker->unique()->text(10),
                "slug"             => $this->faker->unique()->text(5),
                "slug_lang"        => $this->faker->unique()->text(10),
                "description"      => $this->faker->text(10),
                "long_description" => $this->faker->text(100),
                "featured"         => ($product->featured) ? false : true,
                "public"           => ($product->public) ? false : true,
                "offer"            => ($product->offer) ? false : true
        ];
        L::shouldReceive('getDefault')->twice()->andReturn('en');

        $repo = new ProdRepoStubLang();
        $repo->enableGeneralFormFilter();
        $updated_product = $repo->update($product->id, $update_data);

        $this->assertEquals($updated_product->code, $update_data["code"]);
        $this->assertNotEquals($product->offer, $update_data["offer"]);
    }

    /**
     * @test
     **/
    public function itUpdateAllProductsDataIfOnDefaultLanguageAndUpdateCachedData()
    {
        list($product_it, $product_en, $update_data) = $this->prepareProductsForBulkUpdate();
        $this->mockLanguageReturnDefaultLang();

        $repo = new ProdRepoStubLang();
        $repo->enableGeneralFormFilter();

        $repo->update($product_it->id, $update_data);

        $repo->findBySlug($product_it->slug);
        $product_update_it = $this->getProductFromDbPassingThruCache($repo, $product_it);
        $this->assertObjectHasAllAttributes($update_data, $product_update_it, ["lang", "slug_lang"]);

        $repo::$current_lang = 'en';
        $product_update_en = $this->getProductFromDbPassingThruCache($repo, $product_en);
        $this->assertObjectHasAllAttributes($update_data, $product_update_en, ["lang", "slug_lang"]);
    }

    /**
     * @test
     **/
    public function itUpdatesASingleProduct_GivenDisabledFormFilter()
    {
        list($product_it, $product_en, $update_data) = $this->prepareProductsForBulkUpdate();
        $this->mockLanguageReturnLang('en');

        $repo = new ProdRepoStubLang();

        $repo->update($product_it->id, $update_data);

        $repo->findBySlug($product_it->slug);
        $product_update_it = $this->getProductFromDbPassingThruCache($repo, $product_it);
        $this->assertObjectHasAllAttributes($update_data, $product_update_it, ["lang", "slug_lang"]);

        $repo::$current_lang = 'en';
        \Cache::forget("product-{$product_en->slug}-" . $product_en->lang);
        $product_update_en = $this->getProductFromDbPassingThruCache($repo, $product_en);
        $this->assertObjectHasAllAttributes($product_en->toArray(), $product_update_en, ['type']);
    }

    /**
     * @test
     **/
    public function itUpdatesASingleProduct_GivenNoDefaultLang()
    {
        list($product_it, $product_en, $update_data) = $this->prepareProductsForBulkUpdate();
        $this->mockLanguageReturnLang('en');

        $repo = new ProdRepoStubLang();
        $repo->enableGeneralFormFilter();

        $repo->update($product_it->id, $update_data);

        $repo->findBySlug($product_it->slug);
        $product_update_it = $this->getProductFromDbPassingThruCache($repo, $product_it);
        $this->assertObjectHasAllAttributes($update_data, $product_update_it, ["lang", "slug_lang", "featured", "offer","public"]);

        $repo::$current_lang = 'en';
        \Cache::forget("product-{$product_en->slug}-" . $product_en->lang);
        $product_update_en = $this->getProductFromDbPassingThruCache($repo, $product_en);
        $this->assertObjectHasAllAttributes($product_en->toArray(), $product_update_en, ['type']);
    }

    

    //@todo handle the creation case and creation of that and when you change language

    /**
     * @return m\MockInterface|\Yay_MockObject
     */
    protected function mockSetGeneralFormFilter($param)
    {
        $mock_product_filter = m::mock('Palmabit\Catalog\Models\Product');
        $mock_product_filter->shouldReceive('setGeneralFormFilterEnabled')
                            ->once()
                            ->with($param);
        return $mock_product_filter;
    }

    /**
     * @return mixed
     */
    protected function mockLanguageReturnDefaultLang()
    {
        return $this->mockLanguageReturnLang($this->default_lang);
    }

    /**
     * @return array
     */
    protected function prepareProductsForBulkUpdate()
    {
        $product_it = $this->make('Palmabit\Catalog\Models\Product', [
                "lang"      => "it",
                "slug"      => "sl_it",
                "slug_lang" => "sl"
        ])->first();
        $product_en = $this->make('Palmabit\Catalog\Models\Product', [
                "lang"      => "en",
                "slug"      => "sl_en",
                "slug_lang" => "sl"
        ])->first();
        $update_data = [
                "code"             => $this->faker->unique()->text(5),
                "name"             => $this->faker->unique()->text(10),
                "description"      => $this->faker->text(10),
                "long_description" => $this->faker->text(100),
                "lang"             => "it",
                "featured"         => ($product_it->featured) ? 0 : 1,
                "public"           => 1,
                "offer"            => ($product_it->offer) ? 0 : 1
        ];
        return array($product_it, $product_en, $update_data);
    }

    protected function mockLanguageReturnLang($lang)
    {
        L::shouldReceive('getDefault')->andReturn($lang);
    }

    /**
     * @param $repo
     * @param $product
     * @return mixed
     */
    protected function getProductFromDbPassingThruCache($repo, $product)
    {
        return $repo->findBySlug($product->slug);
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
}

class ImgRepoStub extends EloquentProductImageRepository
{
    public function getBinaryData()
    {
        return 1;
    }
}