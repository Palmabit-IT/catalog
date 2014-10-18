<?php  namespace Palmabit\Catalog\Tests;

use App;
use Illuminate\Support\MessageBag;
use Palmabit\Catalog\Tests\Traits\StubTrait;
use Mockery as m;
use Palmabit\Library\Exceptions\ValidationException;

/**
 * Test CategoryControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CategoryControllerTest extends DbTestCase
{
    use StubTrait;

    protected $category_repository;

    public function setUp()
    {
        parent::setUp();
        $this->category_repository = App::make('category_repository');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canChangeProductOrders()
    {
        $this->make($this->category_name, $this->getCategoryModelStub());

        $new_order = 3;
        $this->action('POST', 'Palmabit\Catalog\Controllers\CategoryController@postChangeOrder', ['id' => 1, 'order' => $new_order]);

        $cat = $this->category_repository->all();
        $this->assertEquals($cat[0]->order, $new_order);

        $this->assertRedirectedToAction("Palmabit\\Catalog\\Controllers\\CategoryController@lists");
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function itchangeProductsOrderWithError()
    {
        $this->action('POST', 'Palmabit\Catalog\Controllers\CategoryController@postChangeOrder');

        $this->assertRedirectedToAction("Palmabit\\Catalog\\Controllers\\CategoryController@lists");
    }

    /**
     * @test
     **/
    public function editCategoryDescription()
    {
        $update_data = [
                "category_id" => 12345, // fake id
                "lang"        => 'it',
                "slug"        => "slug",
                "description" => "fake desc"
        ];
        $this->validateWithSuccess();
        $this->repositorySaveWithSuccess($update_data);

        $this->route('POST', 'category.modifica.descrizione', $update_data);

        $this->assertRedirectedToRoute('category.modifica', ['id' => $update_data["category_id"]]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function handleValidationErrors_WhenEditingCategoryDescription()
    {
        $update_data = [
                "category_id" => 12345, // fake id
                "lang"        => 'it',
                "slug"        => "slug",
                "description" => "fake desc"
        ];
        $this->validateWithErrors();
        $this->route('POST', 'category.modifica.descrizione', $update_data);

        $this->assertRedirectedToRoute('category.modifica', ['id' => $update_data["category_id"]]);
        $this->assertSessionHas('errors');
    }

    /**
     * @param $update_data
     */
    protected function repositorySaveWithSuccess($update_data)
    {
        $repository_save = m::mock('StdClass')
                            ->shouldReceive('updateDescription')
                            ->once()
                            ->with($update_data)
                            ->getMock();
        App::instance('category_repository', $repository_save);
    }

    protected function validateWithSuccess()
    {
        $validator = m::mock('StdClass')
                ->shouldReceive('validate')
                ->once()
                ->andReturn(true)
                ->getMock();
        App::instance('category_description_validator', $validator);
    }

    protected function validateWithErrors()
    {
        $validator = m::mock('StdClass')
                      ->shouldReceive('validate')
                      ->once()
                      ->andReturn(false)
                      ->shouldReceive('getErrors')
                      ->once()
                      ->andReturn(new MessageBag())
                      ->getMock();
        App::instance('category_description_validator', $validator);
    }
}
 