<?php namespace Palmabit\Catalog\Tests;

use Prodotti\Repository\ProdottoRepository;
use Mockery as m;

class ProdottiRepositoryTest extends TestCase {

    protected $r;
    protected $faker;


    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate:refresh');

        $this->r = new CatRepoStubLingua();
        $this->faker = Faker\Factory::create();
    }

    public function tearDown()
    {
        m::close();
    }

	public function testAllWorks()
	{
        $this->prepareFakeData();

        $app = m::mock('AppMock');
        $app->shouldReceive('instance')->once()->andReturn($app);

        Illuminate\Support\Facades\Facade::setFacadeApplication($app);
        Illuminate\Support\Facades\Config::swap($config = m::mock('ConfigMock'));

        $config->shouldReceive('get')->once()->andReturn(5);

        $objs = $this->r->all();
        $this->assertEquals(5, count($objs));
	}

    public function testFindBySlugWorks()
    {
        $this->prepareFakeData();
        $obj = $this->r->findBySlug(2);
        $this->assertTrue($obj->exists);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testFindBySlugThrowsModelNotFoundException()
    {
       $this->r->findBySlug(1);
    }

    public function testUltimiInEvidenzaWorks()
    {
        $this->prepareFakeData();
        $objs = $this->r->ultimiInEvidenza();
        $this->assertEquals(1, count($objs));
    }

    public function testUpdateWorks()
    {
        $this->prepareFakeData();

        $obj = $this->r->update(2, ["lang" => "en"]);
        $this->assertEquals("en", $obj->lang);
    }

    public function testCreateWorks()
    {
        $descrizione = "desc";
        $data = [
            "descrizione" => $descrizione,
            "codice" => "codice",
            "nome" => "nome",
            "slug" => "slug",
            "slug_lingua" => "",
            "descrizione_estesa" => "",
            "in_evidenza" => 1,
        ];
        $obj = $this->r->create($data);
        $this->assertTrue($obj instanceof Prodotto);
        $this->assertEquals($descrizione, $obj->descrizione);
    }

    public function testAssociaCategoriaSuccess()
    {
        $this->prepareFakeData();
        foreach(range(1,5) as $key)
        {
            $cat = Categoria::create([
                              "descrizione" => $this->faker->text(10),
                              "slug" => $this->faker->unique()->text(10),
                              "lang" => "it",
                              "slug_lingua" => $this->faker->text(10),
                              ]);
            $cat->prodotto()->attach([$cat->id]);
        }

        $prodotto = $this->r->find(1);
        $this->r->associaCategoria($prodotto->id, 3);
        $cat_id = $prodotto->categoria()->first()->id;
        $this->assertEquals(3,$cat_id);
        $numero_cat = $prodotto->categoria()->count();
        $this->assertEquals(1, $numero_cat);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testAssociaCategoriaThrowsModelNotFoundException()
    {
        $this->r->associaCategoria(1,2);
    }


    protected function prepareFakeData()
    {
        $faker = $this->faker;

        foreach(range(1,5) as $key)
        {
            Prodotto::create([
                             "codice" => $faker->text(5),
                             "nome" => $faker->text(10),
                             "slug" => $key,
                             "slug_lingua" => "slug_lingua",
                             "lang" => 'it',
                             "descrizione" => $faker->text(10),
                             "descrizione_estesa" => $faker->text(100),
                             "in_evidenza" => $key == 5 ? true : false,
                             ]);
        }
    }

    /**
     * @test
     */
    public function it_returns_accessory_related_to_product_and_category()
    {
        $faker = $this->faker;
        $prod = Prodotto::create([
                         "codice" => $faker->text(5),
                         "nome" => $faker->text(10),
                         "slug" => "slug1",
                         "slug_lingua" => "slug_lingua",
                         "lang" => 'it',
                         "descrizione" => $faker->text(10),
                         "descrizione_estesa" => $faker->text(100),
                         "in_evidenza" => false,
                         ]);
        $cat = Categoria::create([
                          "descrizione" => $this->faker->text(10),
                          "slug" => $this->faker->unique()->text(10),
                          "lang" => "it",
                          "slug_lingua" => $this->faker->text(10),
                          ]);
        $prod->categoria()->attach(1);
        $acc1 = Accessori::create([
                          "descrizione" => "accessorio_prodotto",
                          "slug" => "slug",
                          "slug_lingua" => "slug",
                          "lang" => "id"
                          ]);
        $prod->accessori()->attach($acc1->id);
        $acc2 = Accessori::create([
                          "descrizione" => "accessorio_categoria",
                          "slug" => "slug1",
                          "slug_lingua" => "slug1",
                          "lang" => "id"
                          ]);
        $cat->accessori()->attach($acc2->id);
        $accessori = $this->r->getAccessori(1);
        $this->assertEquals(2,count($accessori));
    }

    /**
     * @test *
     */
    public function it_associa_accessorio_a_prodotto()
    {
        $faker = $this->faker;
        $prod = Prodotto::create([
                                 "codice" => $faker->text(5),
                                 "nome" => $faker->text(10),
                                 "slug" => "slug1",
                                 "slug_lingua" => "slug_lingua",
                                 "lang" => 'it',
                                 "descrizione" => $faker->text(10),
                                 "descrizione_estesa" => $faker->text(100),
                                 "in_evidenza" => false,
                                 ]);
        $acc1 = Accessori::create([
                                  "descrizione" => "accessorio_prodotto",
                                  "slug" => "slug",
                                  "slug_lingua" => "slug",
                                  "lang" => "id"
                                  ]);
        $this->r->associaAccessorio(1, 1);
        $accessori = $this->r->getAccessori(1);
        $this->assertEquals(1,count($accessori));

    }

    /**
     * @test *
     */
    public function it_deassocia_un_accessorio()
    {
        $faker = $this->faker;
        $prod = Prodotto::create([
                                 "codice" => $faker->text(5),
                                 "nome" => $faker->text(10),
                                 "slug" => "slug1",
                                 "slug_lingua" => "slug_lingua",
                                 "lang" => 'it',
                                 "descrizione" => $faker->text(10),
                                 "descrizione_estesa" => $faker->text(100),
                                 "in_evidenza" => false,
                                 ]);
        $acc1 = Accessori::create([
                                  "descrizione" => "accessorio_prodotto",
                                  "slug" => "slug",
                                  "slug_lingua" => "slug",
                                  "lang" => "id"
                                  ]);
        $acc2 = Accessori::create([
                                  "descrizione" => "accessorio_categoria",
                                  "slug" => "slug1",
                                  "slug_lingua" => "slug1",
                                  "lang" => "id"
                                  ]);
        $prod->accessori()->attach($acc1->id);
        $prod->accessori()->attach($acc2->id);
        $this->r->deassociaAccessorio(1,1);
        $accessori = $this->r->getAccessori(1);
        $this->assertEquals(1,count($accessori));
    }


}

class CatRepoStubLingua extends ProdottoRepository
{
    public function getLingua()
    {
        return 'it';
    }

}