<?php  namespace Palmabit\Catalog\Models; 

use Palmabit\Catalog\Tests\DbTestCase;
use Palmabit\Catalog\Tests\Traits\StubTrait;

class CategoryTest extends DbTestCase  {
    use StubTrait;

    /**
     * @test
     **/
    public function getCurrentLanguageDescription()
    {
        list($category, $category_description) = $this->createCategoryWithDescription();
        \L::shouldReceive('get')->andReturn('it');

        $this->assertEquals($category->description, $category_description->description,'The presenter description does not match:');
    }

    /**
     * @test
     **/
    public function getCurrentSlugDescription_DependingOnLanguage()
    {
        list($category, $category_description) = $this->createCategoryWithDescription();
        \L::shouldReceive('get')->andReturn('it');

        $this->assertEquals($category->slug_lang, $category_description->slug,'The presenter slug does not match:');
    }
}
 