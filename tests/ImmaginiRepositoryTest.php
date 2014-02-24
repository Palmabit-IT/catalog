<?php

use Prodotti\Repository\ImmagineRepository;

class ImmaginiRepositoryTest extends TestCase {

    protected $repo;
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Faker\Factory::create();
        $this->repo = new ImmaginiRepositoryStub;

        Artisan::call('migrate:refresh');
    }

    public function testCreateWorks()
    {
        $this->creaProdotto();
        $data = [
            "descrizione" => "desc",
            "prodotto_id" => 1,
            "in_evidenza" => 1,
            "immagine_url" => "",
        ];
        $obj = $this->repo->create($data);
        $this->assertTrue(is_a($obj,"ImmaginiProdotto"));
        $this->assertEquals("desc", $obj->descrizione);
    }

    public function testDeleteWorks()
    {
        $this->creaProdotto();
        $data = [
            "descrizione" => "desc",
            "prodotto_id" => 1,
            "in_evidenza" => 1,
            "immagine_url" => "",
        ];
        $obj = $this->repo->create($data);
        $this->repo->delete(1);
        $numero_cat = Categoria::all()->count();
        $this->assertEquals(0, $numero_cat);
    }

    public function testCambiaEvidenzaWorks()
    {
        $this->creaProdotto();
        // creazione categorie
        $data = [
            "descrizione" => "desc1",
            "prodotto_id" => 1,
            "in_evidenza" => 1,
            "immagine_url" => "",
        ];
        $this->repo->create($data);
        $data = [
            "descrizione" => "desc2",
            "prodotto_id" => 1,
            "in_evidenza" => 0,
            "immagine_url" => "",
        ];
        $this->repo->create($data);
        // creazione prodotto
        $faker = $this->faker;
        Prodotto::create([
                         "codice" => $faker->text(5),
                         "nome" => $faker->text(10),
                         "slug" => "slug_1",
                         "slug_lingua" => "slug_lingua",
                         "lang" => 'it',
                         "descrizione" => $faker->text(10),
                         "descrizione_estesa" => $faker->text(100),
                         "in_evidenza" => 1,
                         ]);

        $this->repo->cambiaEvidenza(2,1);
        $img1 = ImmaginiProdotto::find(1);
        $img2 = ImmaginiProdotto::find(2);
        $this->assertEquals(0,$img1->in_evidenza);
        $this->assertEquals(1,$img2->in_evidenza);
    }

    protected function creaProdotto()
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
        Prodotto::create($data);
    }

    /**
     * @expectedException Exceptions\NotFoundException
     */
    public function testCambiaEvidenzaThrowsNotFoundException()
    {
        $this->repo->cambiaEvidenza(1,1);
    }

}

class ImmaginiRepositoryStub extends ImmagineRepository
{
    protected function getBinaryData()
    {
        return "123123121233";
    }
}