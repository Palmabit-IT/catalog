<?php  namespace Palmabit\Catalog\Tests\Traits; 

trait ProductStubTrait {

    protected function getModelStub()
    {
        return [
                "code"                                       => $this->faker->text(5),
                "lang"                                       => 'it',
                "featured"                                   => (integer)$this->faker->boolean(50),
                "public"                                     => (integer)$this->faker->boolean(50),
                "offer"                                      => (integer)$this->faker->boolean(50),
                "order"                                      => 1,
                "stock"                                      => (integer)$this->faker->boolean(50),
                "with_vat"                                   => (integer)$this->faker->boolean(50),
                "video_link"                                 => '',
                "professional"                               => (integer)$this->faker->boolean(50),
                "price1"                                     => $this->fakePrice(),
                "price2"                                     => $this->fakePrice(),
                "price3"                                     => $this->fakePrice(),
                "price4"                                     => $this->fakePrice(),
                "quantity_pricing_enabled"                   => (integer)$this->faker->boolean(50),
                "quantity_pricing_quantity"                  => (integer)$this->faker->boolean(50),
                "quantity_pricing_quantity_non_professional" => (integer)$this->faker->boolean(50),
        ];
    }

    protected function getProductDescriptionStub($product = null)
    {
        return [
                "product_id"       => $product ? $product->id : "",
                "name"             => $this->faker->unique()->text(20),
                "description"      => $this->faker->unique()->text(100),
                "long_description" => $this->faker->unique()->text(100),
                "lang"             => $this->current_lang,
                "slug"             => $this->faker->unique()->lexify('??????????')
        ];
    }

    /**
     * @return string
     */
    protected function fakePrice()
    {
        return $this->faker->randomNumber(2) . '.' . $this->faker->randomNumber(2);
    }
} 