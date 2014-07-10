<?php  namespace Palmabit\Catalog\Tests; 

use Illuminate\Support\Facades\Config;
use Palmabit\Catalog\Presenters\PresenterProducts;

class PresenterProductsTest extends DbTestCase  {

    protected $flags_path;

    public function setUp()
    {
        parent::setUp();
        $this->flags_path = Config::get('catalog::flags.flags_path');
    }

    /**
     * @test
     **/
    public function canGetHisFlag()
    {
        $product_it = $this->make('Palmabit\Catalog\Models\Product', [
            "lang" => 'it',
            "slug" => 'slug',
            "slug_lang" => 'slug'
        ]);
        $presenter = new PresenterProducts($product_it[0]);

        $flag = $presenter->flag;

        $expected_flag = "<img class=\"product-flag\" src=\"{$this->flags_path}/it.jpg\" alt=\"it\" />";
        $this->assertEquals($expected_flag, $flag);
    }
    
    /**
     * @test
     **/
    public function canGetAvailableProductFlags()
    {
        $product_it = $this->make('Palmabit\Catalog\Models\Product', [
            "lang" => 'it',
            "slug" => 'slug',
            "slug_lang" => 'slug'
        ]);
        $this->make('Palmabit\Catalog\Models\Product', [
            "lang" => 'en',
            "slug" => 'slug',
            "slug_lang" => 'slug'
        ]);
        $presenter = new PresenterProducts($product_it[0]);

        $flags = $presenter->availableflags;

        $expected_flags = "<img class=\"product-flag\" src=\"{$this->flags_path}/it.jpg\" alt=\"it\" />        <img class=\"product-flag\" src=\"{$this->flags_path}/en.jpg\" alt=\"en\" />";

//        $this->assertEquals($expected_flags, $flags);
    }

    protected function getModelStub()
    {
        return [
            "code" => $this->faker->unique()->text(5),
            "name" => $this->faker->unique()->text(10),
            "slug" => $this->faker->unique()->text(5),
            "slug_lang" => $this->faker->unique()->text(10),
            "lang" => 'it',
            "description" => $this->faker->text(10),
            "long_description" => $this->faker->text(100),
            "featured" => $this->faker->boolean(50),
            "public" => 1,
            "offer" => 0
        ];
    }
}
 