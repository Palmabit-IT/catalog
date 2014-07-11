<?php  namespace Palmabit\Catalog\Tests\Traits;

Trait StubTrait
{
    protected function getModelStub()
    {
        return [
        ];
    }

    protected function getProductModelStub()
    {
        return [
                "code"             => $this->faker->unique()->text(5),
                "name"             => $this->faker->unique()->text(10),
                "slug"             => $this->faker->unique()->text(5),
                "slug_lang"        => $this->faker->unique()->text(10),
                "lang"             => 'it',
                "description"      => $this->faker->text(10),
                "long_description" => $this->faker->text(100),
                "featured"         => $this->faker->boolean(50),
                "public"           => $this->faker->boolean(50),
                "offer"            => $this->faker->boolean(50)
        ];
    }
} 