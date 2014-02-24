<?php 
/**
 * Test PresenterProdottiTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Presenters\PresenterProdotti;

class PresenterProdottiTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate:refresh');
    }
    
    public function testGetToggleWorks()
    {
        // test disabled
        $prodotto = new Prodotto();
        $presenter = new PresenterProdotti($prodotto);
        $disabled = $presenter->get_toggle;
        $this->assertEquals('data-toggle="" disabled="disabled"', $disabled);
        // test enabled
        $prodotto->exists = true;
        $presenter = new PresenterProdotti($prodotto)
        ;
        $enabled= $presenter->get_toggle;
        $this->assertEquals('data-toggle="tab"', $enabled);
    }

    public function testTagsWorks()
    {
        $this->creaProdottiTags();
        $prodotto = Prodotto::find(1);
        $presenter = new PresenterProdotti($prodotto);
        $tags = $presenter->tags();
        $this->assertEquals(1, $tags[0]["prodotto_id"]);
    }

    protected function creaProdottiTags($slug = "slug")
    {
        $descrizione = "desc";
        $data = [
            "descrizione" => $descrizione,
            "codice" => "codice",
            "nome" => "nome",
            "slug" => $slug,
            "slug_lingua" => "",
            "descrizione_estesa" => "",
            "in_evidenza" => 1,
        ];
        Prodotto::create($data);

        Tags::create([
                     "descrizione" => "desc",
                     "prodotto_id" => 1
                     ]);
    }

    /** @test **/
    public function it_returns_tags_distinct()
    {
        $this->creaProdottiTags("slug1");
        // crea un'altro tag con la stessa desc
        Tags::create([
                     "descrizione" => "desc",
                     "prodotto_id" => 1
                     ]);
        $prodotto = Prodotto::find(1);
        $presenter = new PresenterProdotti($prodotto);
        $tags = $presenter->tags_select();
        $this->assertEquals(1, count($tags));
        $this->assertEquals("desc", $tags["desc"]);
    }

    /** @test **/
    public function it_ritorna_gli_accessori()
    {
        // preparazione dati
        $this->creaProdottiTags("slug1");
        Accessori::create([
                          "descrizione" => "desc",
                          "slug" => "slug",
                          "slug_lingua" => "slug",
                          "lang" => "it"
                          ]);
        $prodotto = Prodotto::find(1);
        $prodotto->accessori()->attach(1);
        $presenter = new PresenterProdotti($prodotto);
        $accessori = $presenter->accessori;
        $this->assertEquals(1, count($accessori));
        $this->assertEquals("slug", $accessori[0]["slug"]);
    }

    /**
     * @test
     */
    public function it_ritorna_accessori_categoria()
    {
        $faker = Faker\Factory::create();
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
                                 "descrizione" => $faker->text(10),
                                 "slug" => $faker->unique()->text(10),
                                 "lang" => "it",
                                 "slug_lingua" => $faker->text(10),
                                 ]);
        $prod->categoria()->attach(1);
        $acc2 = Accessori::create([
                                  "descrizione" => "accessorio_categoria",
                                  "slug" => "slug1",
                                  "slug_lingua" => "slug1",
                                  "lang" => "id"
                                  ]);
        $cat->accessori()->attach($acc2->id);
        $presenter = new PresenterProdotti($prod);
        $acc = $presenter->accessori_categoria;
        $this->assertEquals(1, count($acc));
        $this->assertEquals("slug1", $acc[0]["slug"]);
    }

}