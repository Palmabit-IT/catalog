<?php
/**
 * Class CategoriaRepository
 *
 * @author jacopo beschi
 */
namespace Category\Repository;

use Categoria;
use Classes\RepositoryInterface;
use Multilingua\Interfaces\MultilinguaRepositoryInterface;
use Multilingua\Traits\LanguageHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Prodotti\Interfaces\AccessoriInterface;
use Accessori;

class CategoriaRepository implements RepositoryInterface, MultilinguaRepositoryInterface, AccessoriInterface
{
    use LanguageHelper;

    /**
     * Se il repository viene usato come gestione admin
     * @var Boolean
     */
    protected $is_admin;

    public function __construct($is_admin = false)
    {
        $this->is_admin = $is_admin;
    }

    /**
     * Ricerca le categorie dalla descrizione
     *
     * @param $descrizione
     * @return null
     */
    public function search($descrizione)
    {
        $cats = Categoria::whereDescrizione($descrizione)->get();
        return $cats->isEmpty() ? null : $cats->all();
    }

    /**
     * Ricerca le categorie dallo slug
     *
     * @param $slug
     * @return null
     */
    public function searchBySlug($slug)
    {
        $cats = Categoria::whereSlug($slug)->get();
        return $cats->isEmpty() ? null : $cats->first();
    }


    /**
     * Effettua l'inserimento
     *
     * @param $descrizione
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function create(array $data)
    {
        return Categoria::create(array(
                                      "descrizione" =>$data["descrizione"],
                                      "slug" => $data["slug"],
                                      "slug_lingua" => $data["slug_lingua"] ? $data["slug_lingua"] : $this->generaSlugLingua($data),
                                      "lang" => $this->getLingua()
                                 ));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $cat = Categoria::whereLang($this->getLingua())
            ->orderBy("descrizione")
            ->get();

        return $cat->isEmpty() ? null : $cat->all();
    }

    /**
     * Effettua il find
     *
     * @param $id
     */
    public function find($id)
    {
        return Categoria::findOrFail($id);
    }

    /**
     * Aggiornamento
     *
     * @param $id
     * @param $descrizione
     * @throws ModelNotFoundException
     */
    public function update($id, array $data)
    {
        $cat = $this->find($id);

        $cat->update(array(
                          "descrizione" => $data["descrizione"],
                          "slug" => $data["slug"]
                     ));

        return $cat;
    }

    /**
     * Delete
     *
     * @param $id
     * @throws ModelNotFoundException
     */
    public function delete($id)
    {
        $cat = $this->find($id);
        return $cat->delete();
    }

    /**
     * Ottiene la risorsa partendo dallo slug lingua
     *
     * @param $slug_lingua
     * @return mixed
     */
    public function findBySlugLingua($slug_lingua)
    {
        $cat= Categoria::whereSlugLingua($slug_lingua)
            ->whereLang($this->getLingua())
            ->get();

        if($cat->isEmpty()) throw new ModelNotFoundException;

        return $cat->first();
    }

    /**
     * Ottiene gli accessori collegati
     *
     * @param $id
     * @return mixed
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getAccessori($id)
    {
        $cat = $this->find($id);
        return $cat->accessori()->get()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function associaAccessorio($categoria_id, $accessorio_id)
    {
        $cat = $this->find($categoria_id);
        $accessorio = Accessori::findOrFail($accessorio_id);
        // attach data
        return $cat->accessori()->save($accessorio);
    }


    /**
     * Deassocia un accessorio
     * @param $prodotto_id
     * @param $accessorio_id
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deassociaAccessorio($categoria_id, $accessorio_id)
    {
        $categoria = $this->find($categoria_id);
        return $categoria->accessori()->detach($accessorio_id);
    }

}