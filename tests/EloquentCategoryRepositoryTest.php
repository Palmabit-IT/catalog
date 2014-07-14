<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Models\CategoryDescription;
use Palmabit\Catalog\Repository\EloquentCategoryRepository;
use Palmabit\Catalog\Models\Category;
use Event;

class EloquentCategoryRepositoryTest extends DbTestCase
{
    protected $faker;
    protected $repo;
    protected $category_name;
    protected $category_description_name;

    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
        $this->repo = new CatRepoStubLang();
        $this->category_name = 'Palmabit\Catalog\Models\Category';
        $this->category_description_name = 'Palmabit\Catalog\Models\CategoryDescription';
    }

    /**
     * @test
     */
    public function createCategoryWithBaseData()
    {
        $cat_data = [
                "name"  => "name",
                "order" => 3,
        ];
        $cat = $this->repo->create($cat_data);
        $this->assertTrue($cat instanceof Category);
        $this->assertObjectHasAllAttributes($cat_data, $cat);
    }

    /**
     * @test
     */
    public function updateCategoryBaseData()
    {
        $created_category = $this->make($this->category_name)->first();

        $new_name = "new name";
        $cat = $this->repo->update($created_category->id, ["name" => $new_name]);

        $this->assertEquals($new_name, $cat->name);
    }

    /**
     * @test
     */
    public function findCategoryDescriptionByDescription()
    {
        $created_category = $this->make($this->category_name)->first();

        $created_category_description =
                $this->make($this->category_description_name, $this->getCategoryDescriptionModelStub($created_category))->first();

        $category_description_found = $this->repo->search($created_category_description->description);

        $this->assertObjectHasAllAttributes($created_category_description->toArray(), $category_description_found[0]);
    }

    /**
     * @test
     */
    public function findCategoryDescriptionBySlug()
    {
        list($created_category, $created_category_description) = $this->createCategoryWithDescription();

        $category_description_found = $this->repo->searchBySlug($created_category_description->slug);

        $this->assertObjectHasAllAttributes($created_category_description->toArray(), $category_description_found);
    }

    /**
     * @test
     **/
    public function canAccessCategoryProperties_AsHisProperties()
    {
        list($created_category, $created_category_description) = $this->createCategoryWithDescription();

        $this->assertEquals($created_category_description->name, $created_category->name);
        $this->assertEquals($created_category_description->image, $created_category->image);

        $created_category_description->image = 12345;
        $new_name = "new name";
        $created_category_description->name = $new_name;

        $updated_category = $this->repo->find($created_category->id);
        $this->assertEquals($updated_category->name, $created_category_description->name);
        $this->assertEquals($updated_category->image, $created_category_description->image);
    }


    public function deleteCategory()
    {
        list($created_category, $created_category_description) = $this->createCategoryWithDescription();

        $this->assertTrue($this->repo->delete($created_category->id));

        $this->assertEquals(0, Category::count());
        $this->assertEquals(0, CategoryDescription::count());
    }

    /**
     * @test
     **/
    public function getAllCategoriesOrderedByDepthAndName()
    {
        $times = 5;
        $this->times($times)->make($this->category_name);

        $all_categories = $this->repo->all();

        $this->assertCount($times, $all_categories);

        $field = "order";
        $this->isOrderedAscBy($field, $all_categories);
    }


    /**
     * @test
     **/
    public function it_gets_only_root_categories()
    {
        $this->times(2)->make($this->category_name, ["depth" => null]);
        $this->times(2)->make($this->category_name, ["depth" => 1]);

        $results = $this->repo->getRootNodes();
        $this->assertEquals(2, count($results));
    }

    /**
     * @test
     */
    public function getSelectArrayWithAllCategoriesAndNames()
    {
        $cat1 = $this->make($this->category_name, ["name" => "1name"])->first();
        $cat2 = $this->make($this->category_name, ["name" => "2name"])->first();

        $expected_data = [
                1  => $cat1->name,
                2  => $cat2->name,
                "" => "Qualsiasi"

        ];
        $this->assertEquals($expected_data, $this->repo->getArrSelectCat());
    }


    /**
     * @test
     **/
    public function it_set_cat_depth()
    {
        $this->make($this->category_name);

        $this->repo->setDepth(1, 1);

        $cat_saved = $this->repo->find(1);
        $this->assertEquals(1, $cat_saved->depth);
    }

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

    /**
     * @return mixed
     */
    protected function createCategoryWithDescription()
    {
        $created_category = $this->make($this->category_name)->first();

        $created_category_description = $this->make($this->category_description_name, $this->getCategoryDescriptionModelStub($created_category))->first();
        return [$created_category, $created_category_description];
    }

    /**
     * @param $field
     * @param $all_categories
     */
    protected function isOrderedAscBy($field, $all_categories)
    {
        $this->assertTrue($all_categories[0]->$field >= $all_categories[1]->$field);
        $this->assertTrue($all_categories[1]->$field >= $all_categories[2]->$field);
        $this->assertTrue($all_categories[3]->$field >= $all_categories[4]->$field);
    }

    /**
     * @test
     **/
    public function canUpdateCategoryDescription()
    {
        list($created_category, $created_category_description) = $this->createCategoryWithDescription();

        $update_data = [
                "category_id" => $created_category->id,
                "lang" => CatRepoStubLang::$current_lang,
                "slug" => $this->faker->unique()->text(20),
                "description" => "fake desc"
        ];

        $this->repo->updateDescription($update_data);

        $created_category_description = CategoryDescription::first();
        $this->assertObjectHasAllAttributes($update_data, $created_category_description);
    }
    
    /**
     * @test
     **/
    public function canCreateCategoryDescription()
    {
        $created_category = $this->make($this->category_name)->first();
        $update_data = [
                "category_id" => $created_category->id,
                "lang" => CatRepoStubLang::$current_lang,
                "slug" => "slug",
                "description" => "fake desc"
        ];

        $this->repo->updateDescription($update_data);

        $created_category_description = CategoryDescription::first();
        $this->assertObjectHasAllAttributes($update_data, $created_category_description);
    }
    
    /**
     * @test
     * @expectedException \Palmabit\Library\Exceptions\NotFoundException
     **/
    public function handleCreationOfCategoryDescriptionWithNoCategoryGiven()
    {
        $update_data = [
                "category_id" => 12345, // fake id
                "lang" => CatRepoStubLang::$current_lang,
                "slug" => "slug",
                "description" => "fake desc"
        ];

        $this->repo->updateDescription($update_data);
    }
}

class CatRepoStubLang extends EloquentCategoryRepository
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