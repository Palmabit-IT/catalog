<?php  namespace Palmabit\Catalog\Tests;

/**
 * Test PresenterCategoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use Palmabit\Catalog\Tests\Traits\StubTrait;
use URLT;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Presenters\PresenterCategory;

class PresenterCategoryTest extends DbTestCase
{
    use StubTrait;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_gets_featured_image()
    {
        $presenter =
                m::mock('Palmabit\Catalog\Presenters\PresenterCategory')->makePartial()->shouldReceive('image')->once()->andReturn(22)->getMock();
        $this->assertEquals(22, $presenter->featured_image);
    }

    /**
     * @test
     **/
    public function it_gets_siblings_with_same_lang()
    {
        $get_mock = m::mock('StdClass')->shouldReceive('get')->andReturn(1)->getMock();
        $mock_where = m::mock('StdClass')->shouldReceive('whereLang')->andReturn($get_mock)->getMock();
        $mock_resource = m::mock('StdClass')->shouldReceive('siblings')->andReturn($mock_where)->getMock();
        $presenter = new PresenterCategory($mock_resource);

        $this->assertEquals(1, $presenter->siblings());
    }

    /**
     * @test
     **/
    public function it_get_translated_link()
    {
        URLT::shouldReceive('action')->once();
        $category = $this->make('Palmabit\Catalog\Models\Category', $this->getCategoryModelStub())->first();
        $this->make('Palmabit\Catalog\Models\CategoryDescription', $this->getCategoryDescriptionModelStub($category))->first();
        $presenter = new PresenterCategory($category);
        $presenter->getLink();
    }

    /**
     * @test
     **/
    public function it_get_empty_link_if_no_slug_given()
    {
      $category = $this->make('Palmabit\Catalog\Models\Category', $this->getCategoryModelStub())->first();
      $presenter = new PresenterCategory($category);
      $this->assertEquals("", $presenter->getLink());
    }

    /**
     * @test
     */
    public function getCategoryDescriptionInGivenLanguage()
    {
        list($category, $category_description) = $this->createCategoryWithDescription();
        $presenter = new PresenterCategory($category);

        $this->assertEquals($presenter->getDescriptionObjectOfLang('it'), $category_description);
    }
}
 