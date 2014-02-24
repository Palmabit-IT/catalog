<?php  namespace Prodotti\Repository; 
/**
 * Class AccessoriRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Classes\RepositoryInterface;
use Multilingua\Interfaces\MultilinguaRepositoryInterface;
use Accessori;
use Multilingua\Traits\LanguageHelper;
use Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccessoriRepository implements RepositoryInterface, MultilinguaRepositoryInterface
{
    use LanguageHelper;

    /**
     * Se il repository viene usato come gestione admin
     * @var Boolean
     */
    protected $is_admin;

    public function __construct($is_admin = true)
    {
        $this->is_admin = $is_admin;
    }

    /**
     * Gets all the objects
     *
     * @return mixed
     * @todo test
     */
    public function all()
    {
        $per_page = Config::get('baseconf.admin_accessori_per_page');
        set_view_paginator('pagination::slider-3');
        $prodotti = Accessori::whereLang($this->getLingua())
            ->orderBy("descrizione")
            ->paginate($per_page);
        return $prodotti->isEmpty() ? null : $prodotti;
    }

    /**
     * Finds a model
     *
     * @param $id
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        return Accessori::findOrFail($id);
    }

    /**
     * Aggiorna un model
     *
     * @param       $id
     * @param array $data
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @todo test
     */
    public function update($id, array $data)
    {
        if(isset($data["slug_lingua"])) unset($data["slug_lingua"]);
        //@todo refactor con trait
        $acc = $this->find($id);

        $acc->update($data);

        return $acc;
    }

    /**
     * Crea un model
     *
     * @param array $data
     * @return mixed
     * @todo test
     */
    public function create(array $data)
    {
        return Accessori::create([
            "descrizione" => $data["descrizione"],
            "slug" => $data["slug"],
            "slug_lingua" => $data["slug_lingua"] ? $data["slug_lingua"] : $this->generaSlugLingua($data),
            "lang" => $this->getLingua()
            ]);
    }

    /**
     * Rimuove la risorsa dal db
     *
     * @param $id
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete($id)
    {
        $acc = $this->find($id);
        return $acc->delete();
    }

    /**
     * Ottiene la risorsa partendo dallo slug lingua
     *
     * @param $slug_lingua
     * @return mixed
     */
    public function findBySlugLingua($slug_lingua)
    {
        $accessori = Accessori::whereSlugLingua($slug_lingua)
            ->whereLang($this->getLingua())
            ->get();

        if($accessori->isEmpty()) throw new ModelNotFoundException;

        return $accessori->first();
    }

}