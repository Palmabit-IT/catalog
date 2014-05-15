<?php  namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Models\Product;

/**
 * Test ProductTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProductTest extends DbTestCase {

    /**
     * @test
     **/
    public function canCheckForAvailability()
    {
        $product = Product::create([
                                   "code" => "12345",
                                   "name" => "name",
                                   "slug" => "slug",
                                   "slug_lang" => "slug_lang",
                                   "lang" => 'it',
                                   "description" => "",
                                   "long_description" => "",
                                   "featured" => 0,
                                   "public" => 1,
                                   "offer" => 0,
                                   "stock" => 1
                                   ]);
        $this->assertTrue($product->isAvailabile());

        $product->update(["stock" => 0]);

        $this->assertFalse($product->isAvailabile());
    }

}
 