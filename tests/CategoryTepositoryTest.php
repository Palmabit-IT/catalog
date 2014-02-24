<?php

use Category\Repository\CategoriaRepository;

class CategoryTepositoryTest extends TestCase {

    protected $faker;
    protected $repo;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Faker\Factory::create();
        $this->repo = new RepoStubLingua();

        Artisan::call('migrate:refresh');
    }

    public function testCreate()
    {
        $desc= "descrizione";
        $cat = $this->repo->create(array("descrizione"=> $desc, "slug" => "slug", "slug_lingua" => "slug") );
        $this->assertTrue($cat instanceof Categoria);
        $this->assertEquals($desc, $cat->descrizione);
    }

    public function testSearch()
    {
        $descrizione = "descrizione";
        Categoria::create(array("descrizione"=>$descrizione, "slug" => "slug", "slug_lingua" => "slug"));

        $cat = $this->repo->search($descrizione);
        $this->assertNotEmpty($cat);

        $cat = $this->repo->search("not found");
        $this->assertEmpty($cat);
    }

    public function testUpdateWorks()
    {
        $desc= "descrizione";
        $cat = $this->repo->create(array("descrizione"=> $desc, "slug" => "slug", "slug_lingua" => "slug") );
        $id =$cat->id;

        $newdesc= "nuova descrizione";
        $cat = $this->repo->update($id,array("descrizione"=> $newdesc, "slug" => "slug") );

        $this->assertEquals($newdesc, $cat->descrizione);
    }

    public function testDeleteWorkd()
    {
        $desc= "descrizione";
        $cat = $this->repo->create(array("descrizione"=> $desc, "slug" => "slug", "slug_lingua" => "slug") );

        $this->assertTrue( $this->repo->delete($cat->id) );
    }
    
    /** @test **/
    public function it_ottiene_gli_accessori_associati()
    {
        $cat = $this->repo->create(array("descrizione"=> $this->faker->text(10), "slug" => "slug", "slug_lingua" => "slug") );
        $acc = Accessori::create([
                                 "descrizione"=>"desc",
                                  "slug" => "slug",
                                  "slug_lingua" => "slug",
                                  "lang" => "id"
                                 ]);
        $cat->accessori()->save($acc);
        $accessori = $this->repo->getAccessori(1);
        $this->assertEquals(1, count($accessori));
    }

    /** @test **/
    public function it_associa_accessorio()
    {
        $cat = $this->repo->create(array("descrizione"=> $this->faker->text(10), "slug" => "slug", "slug_lingua" => "slug") );
        $acc = Accessori::create([
                                 "descrizione"=>"desc",
                                   "slug" => "slug",
                                  "slug_lingua" => "slug",
                                  "lang" => "id"
                                ]);
        $this->repo->associaAccessorio(1,1);
        $accessori = $this->repo->getAccessori(1);
        $this->assertEquals(1, count($accessori));
    }

    /** @test **/
    public function it_deassocia_accessorio()
    {
        $cat = $this->repo->create(array("descrizione"=> $this->faker->text(10), "slug" => "slug", "slug_lingua" => "slug") );
        $acc = Accessori::create([
                                 "descrizione"=>"desc",
                                 "slug" => "slug",
                                 "slug_lingua" => "slug",
                                 "lang" => "id"
                                 ]);
        $this->repo->associaAccessorio(1,1);
        $this->repo->deassociaAccessorio(1,1);
        $accessori = $this->repo->getAccessori(1);
        $this->assertEquals(0, count($accessori));
    }

}

class RepoStubLingua extends CategoriaRepository
{
    public function getLingua()
    {
        return 'it';
    }

}