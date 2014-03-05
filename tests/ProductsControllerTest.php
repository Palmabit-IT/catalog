<?php  namespace Palmabit\Catalog\Tests; 
use Mockery as m;
use App;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Exceptions\ValidationException;
use Illuminate\Support\MessageBag;
/**
 * Test ProductTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class ProductsControllerTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
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
        $slug_lang = "ss";

        $mock_repo = m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->shouldReceive('attachProduct')->once()->with($first_id,$second_id)->andReturn(true)->getMock();
        App::instance('product_repository', $mock_repo);
        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')->once()->andReturn(true)->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST','Palmabit\Catalog\Controllers\ProductsController@postAttachProduct','',["first_product_id" => $first_id, "second_product_id" => $second_id, "slug_lang" => $slug_lang]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit',['slug_lang' => $slug_lang]);
        $this->assertSessionHas(['message_accessories']);
    }

    /**
     * @test
     **/
    public function it_redirect_with_errors_if_validation_fails_on_attach_product()
    {
        $slug_lang = "ss";

        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')
            ->once()
            ->andThrow(new ValidationException)
            ->shouldReceive('getErrors')
            ->once()
            ->andReturn(new MessageBag(["model" => "model"]))->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST','Palmabit\Catalog\Controllers\ProductsController@postAttachProduct','',[ "slug_lang" => $slug_lang]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit',['slug_lang' => $slug_lang]);

        $this->assertSessionHasErrors(['model']);
    }

    /**
     * @test
     **/
    public function it_redirect_with_errors_if_doesnt_find_the_model_on_attach_products()
    {
        $slug_lang = "ss";

        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')
            ->once()
            ->andThrow(new NotFoundException())
            ->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST','Palmabit\Catalog\Controllers\ProductsController@postAttachProduct','',[ "slug_lang" => $slug_lang]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit',['slug_lang' => $slug_lang]);

        $this->assertSessionHasErrors(['model']);
    }

    /**
     * @test
     **/
    public function it_call_repository_then_redirect_the_user_on_attach_products()
    {
        $first_id = 1;
        $second_id = 2;
        $slug_lang = "ss";

        $mock_repo = m::mock('Palmabit\Catalog\Repository\EloquentProductRepository')->shouldReceive('detachProduct')->once()->with($first_id,$second_id)->andReturn(true)->getMock();
        App::instance('product_repository', $mock_repo);
        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')->once()->andReturn(true)->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST','Palmabit\Catalog\Controllers\ProductsController@postDetachProduct','',["first_product_id" => $first_id, "second_product_id" => $second_id, "slug_lang" => $slug_lang]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit',['slug_lang' => $slug_lang]);
        $this->assertSessionHas(['message_accessories']);
    }


    /**
     * @test
     **/
    public function it_redirect_with_errors_if_doesnt_find_the_model_on_detach_products()
    {
        $slug_lang = "ss";

        $mock_validator = m::mock('Palmabit\Catalog\Validators\ProductsProductsValidator')->shouldReceive('validate')
            ->once()
            ->andThrow(new NotFoundException())
            ->getMock();
        App::instance('Palmabit\Catalog\Validators\ProductsProductsValidator', $mock_validator);

        $this->action('POST','Palmabit\Catalog\Controllers\ProductsController@postDetachProduct','',[ "slug_lang" => $slug_lang]);

        $this->assertRedirectedToAction('Palmabit\Catalog\Controllers\ProductsController@getEdit',['slug_lang' => $slug_lang]);
        $this->assertSessionHasErrors(['model']);
    }
}
 