<?php namespace Palmabit\Catalog\Tests;
/**
 * Test PresenterProdottiTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Presenters\PresenterProducts;

class ProductPresenterTest extends TestCase {

    public function testGetToggleWorks()
    {
        // test disabled
        $product = new Product();
        $presenter = new PresenterProducts($product);
        $disabled = $presenter->get_toggle;
        $this->assertEquals('data-toggle="" disabled="disabled"', $disabled);
        // test enabled
        $product->exists = true;
        $presenter = new PresenterProducts($product)
        ;
        $enabled= $presenter->get_toggle;
        $this->assertEquals('data-toggle="tab"', $enabled);
    }
}