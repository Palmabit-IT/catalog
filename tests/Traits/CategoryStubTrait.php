<?php  namespace Palmabit\Catalog\Tests\Traits;

use Palmabit\Catalog\Tests\CatRepoStubLang;

trait CategoryStubTrait
{
    public function getModelStub()
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
                "lang"        => CatRepoStubLang::$current_lang,
        ];
    }
} 