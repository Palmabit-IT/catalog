<?php  namespace Palmabit\Catalog\Tests\Traits;

Trait StubTrait
{
    protected $category_name = 'Palmabit\Catalog\Models\Category';
    protected $category_description_name = 'Palmabit\Catalog\Models\CategoryDescription';

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

    public function getCategoryModelStub()
    {
        return [
                "name"  => $this->faker->unique()->name,
                "order" => $this->faker->randomNumber(2),
                "image" => $this->faker->randomNumber(9),
                'depth' => $this->faker->unique()->randomNumber(3)

        ];
    }

    /**
     * @param $created_category
     * @return array
     */
    protected function getCategoryDescriptionModelStub($created_category)
    {
        return [
                "description" => $this->faker->text(20),
                "slug"        => $this->faker->text(10),
                "category_id" => $created_category->id,
                "lang"        => 'it',
        ];
    }

    /**
     * @return mixed
     */
    protected function createCategoryWithDescription()
    {
        $created_category = $this->make($this->category_name, $this->getCategoryModelStub())->first();

        $created_category_description = $this->make($this->category_description_name, $this->getCategoryDescriptionModelStub($created_category))->first();
        return [$created_category, $created_category_description];
    }

    /**
     * @param $slug_lang
     */
    protected function createNProductsWithSameSlugLang($times, $slug_lang)
    {
        $this->times($times)->make('Palmabit\Catalog\Models\Product', function () use ($slug_lang)
        {
            return array_merge($this->getProductModelStub(), [
                    "slug_lang" => $slug_lang,
                    "lang"      => $this->faker->unique()->lexify('??')
            ]);
        });
    }
} 