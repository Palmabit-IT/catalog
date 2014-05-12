<?php  namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Services\AlignProducts;
/**
 * Test AlignProducts
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class AlignProductTest extends DbTestCase {

    protected $faker;

    protected $align;

    public function setUp()
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
        $this->align = new AlignProducts();
    }

    /**
     * @test
     **/
    public function itAlignProducsWithDifferentLanguagesBasedOnCode()
    {
        $number_products = 5;
        $this->createProductsWithManyLanguages($number_products);

        $this->align->alignData();

        foreach(range(1, $number_products -1 ) as $index)
        {
            $products_stack = Product::whereCode($index)->get()->all();
            $this->assertEquals($products_stack[0]->slug_lang, $products_stack[1]->slug_lang);
        }
    }
    
    /**
     * @test
     **/
    public function itCleanProductsWithSameCodeAndLang()
    {
        $this->createTwoProductsWithSameCodeAndLang();

        $this->align->cleanProducts();

        $expected_products_number = 1;
        $this->assertEquals($expected_products_number,Product::count());
    }

    /**
     * Creates n random products
     *
     * @param $number_products
     */
    protected function createProductsWithManyLanguages($number_products = 5)
    {
        foreach(range(1, $number_products-1) as $key)
        {
            $this->createProduct($key, "it");
            $this->createProduct($key, "en");
        }
        // last product only italian
        $this->createProduct($number_products, "it");

    }

    /**
     * @param $key
     * @param $faker
     */
    protected function createProduct($key, $lang)
    {
        $faker = $this->faker;

        Product::create([
                        "code"             => $key,
                        "name"             => $faker->text(10),
                        "slug"             => $key,
                        "slug_lang"        => $faker->text(10),
                        "lang"             => $lang,
                        "description"      => $faker->text(10),
                        "long_description" => $faker->text(100),
                        "featured"         => $key == 5 ? true : false,
                        "public"           => 1,
                        "offer"            => 0
                        ]);
    }

    protected function createTwoProductsWithSameCodeAndLang()
    {
        Product::create([
                        "code"             => 1,
                        "name"             => "name1",
                        "slug"             => "slug1",
                        "slug_lang"        => "slug1",
                        "lang"             => "it",
                        "description"      => "",
                        "long_description" => "",
                        "featured"         => 0,
                        "public"           => 1,
                        "offer"            => 0
                        ]);

        Product::create([
                        "code"             => 1,
                        "name"             => "name2",
                        "slug"             => "slug2",
                        "slug_lang"        => "slug2",
                        "lang"             => "it",
                        "description"      => "",
                        "long_description" => "",
                        "featured"         => 0,
                        "public"           => 1,
                        "offer"            => 0
                        ]);
    }

}
 