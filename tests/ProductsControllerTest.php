<?php  namespace Palmabit\Catalog\Tests;

use Mockery as m;
use App;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Tests\Traits\StubTrait;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Exceptions\ValidationException;
use Illuminate\Support\MessageBag;

/**
 * Test ProductTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class ProductsControllerTest extends DbTestCase
{
    use StubTrait;

    protected $product_repository;

    public function setUp()
    {
        parent::setUp();
        $this->product_repository = App::make('product_repository');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_call_validator_and_repository_then_redirect_the_user_on_attach_product()
    {
        $first_id = 1;
        $second_id = 2;

        $mock_repo =
                m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->shouldReceive('attachProduct')->once()->with($first_id, $second_id)
                 ->andReturn(true)->getMock();
        App::instance('product_repository', $mock_repo);
        $mock_validator =
                m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')->once()->andReturn(true)->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@postAttachProduct', '', ["first_product_id"  => $first_id,
                                                                                                        "second_product_id" => $second_id]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit', ['id' => $first_id]);
        $this->assertSessionHas(['message_accessories']);
    }

    /**
     * @test
     **/
    public function it_redirect_with_errors_if_validation_fails_on_attach_product()
    {
        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')
                           ->once()
                           ->andThrow(new ValidationException)
                           ->shouldReceive('getErrors')
                           ->once()
                           ->andReturn(new MessageBag(["model" => "model"]))->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@postAttachProduct', '', ["first_product_id" => 1]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit', ['id' => 1]);

        $this->assertSessionHasErrors(['model']);
    }

    /**
     * @test
     **/
    public function it_redirect_with_errors_if_doesnt_find_the_model_on_attach_products()
    {
        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')
                           ->once()
                           ->andThrow(new NotFoundException())
                           ->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@postAttachProduct', '', ["first_product_id" => 1]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit', ['id' => 1]);

        $this->assertSessionHasErrors(['model']);
    }

    /**
     * @test
     **/
    public function it_call_repository_then_redirect_the_user_on_attach_products()
    {
        $first_id = 1;
        $second_id = 2;

        $mock_repo =
                m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->shouldReceive('detachProduct')->once()->with($first_id, $second_id)
                 ->andReturn(true)->getMock();
        App::instance('product_repository', $mock_repo);
        $mock_validator =
                m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')->once()->andReturn(true)->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@postDetachProduct', '', ["first_product_id"  => $first_id,
                                                                                                        "second_product_id" => $second_id]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit', ['id' => $first_id]);
        $this->assertSessionHas(['message_accessories']);
    }


    /**
     * @test
     **/
    public function it_redirect_with_errors_if_doesnt_find_the_model_on_detach_products()
    {
        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')
                           ->once()
                           ->andThrow(new NotFoundException())
                           ->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@postDetachProduct', '', ["first_product_id" => 1]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit', ['id' => 1]);
        $this->assertSessionHasErrors(['model']);
    }

    /**
     * @test
     **/
    public function it_call_duplicate_and_redirect_with_success()
    {
        $prod_stub = new \StdClass();
        $slug_lang = "sl";
        $prod_stub->slug_lang = $slug_lang;
        $mock_repo = m::mock('StdClass')->shouldReceive('duplicate')->once()->with(1)->andReturn($prod_stub)->getMock();
        App::instance('product_repository', $mock_repo);
        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@duplicate', '', ["id" => 1, "slug_lang" => "old_slug_lang"]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@lists', ['id' => 1]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function it_call_duplicate_and_redirects_with_errors()
    {
        $mock_repo = m::mock('StdClass')->shouldReceive('duplicate')->once()->with(1)->andThrow(new NotFoundException())->getMock();
        App::instance('product_repository', $mock_repo);
        $slug_lang = "sl";
        $this->action('POST', 'Palmabit\Catalog\Controllers\ProductsController@duplicate', '', ["id" => 1, "slug_lang" => $slug_lang]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@lists', ['id' => 1]);
        $this->assertSessionHasErrors();
    }
}
 