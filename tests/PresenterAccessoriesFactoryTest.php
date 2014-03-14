<?php  namespace Palmabit\Catalog\Tests; 
use Mockery as m;
use App;
use Palmabit\Catalog\Presenters\PresenterAccessoriesFactory;

/**
 * Test PresenterAccessoriesTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class PresenterAccessoriesFactoryTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     * @group prodf
     **/
    public function it_create_a_collection_of_product_presenter_given_product()
    {
        $data1 = [
                       "description" => "desc",
                       "code" => "code",
                       "name" => "name",
                       "slug" => "slug",
                       "slug_lang" => "",
                       "long_description" => "",
                       "featured" => 1,
                       "public" => 1,
                       "offer" => 1,
                       "stock" => 4,
                       "with_vat" => 1,
                       "video_link" => "http://www.google.com/video/12312422313",
                       "professional" => 1,
                       "public_price" => "12.22",
                       "logged_price" => "8.21",
                       "professional_price" => "2.12",
                   ];
        $data2 = $data1;
        $data2["slug"] = "slug2";
        $repo_product = App::make('product_repository');
        $prod1 = $repo_product->create($data1);
        $prod2 = $repo_product->create($data2);
        $repo_product->attachProduct($prod1->id, $prod2->id);
        $factory = new PresenterAccessoriesFactory();

        $presenter = $factory->create($prod1->id);

        $this->assertInstanceOf('Palmabit\Library\Presenters\PresenterCollection', $presenter);
        $this->assertEquals(1, count($presenter));
        $this->assertEquals($prod2->slug, $presenter->first()->slug);
    }

}
 