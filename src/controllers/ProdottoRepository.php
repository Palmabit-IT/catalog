<?php namespace Palmabit\Catalog\Repository;
/**
 * Class ProdottoRepository
 *
 * @author jacopo beschi
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Classes\RepositoryInterface;
use Multilingua\Interfaces\MultilinguaRepositoryInterface;
use Prodotti\Interfaces\AccessoriInterface;
use Multilingua\Traits\LanguageHelper;
use Categoria;
use Prodotto;
use L;
use Config;
use DB;
use Accessori;
use Cache;

class ProdottoRepository implements RepositoryInterface, MultilinguaRepositoryInterface, AccessoriInterface
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
     * {@inheritdoc}
     */
    public function all()
    {
        $per_page = Config::get('baseconf.admin_per_page');
        set_view_paginator('pagination::slider-3');
        $prodotti = Prodotto::whereLang($this->getLingua())
            ->orderBy("ordine","nome")
            ->paginate($per_page);
        return $prodotti->isEmpty() ? null : $prodotti;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Prodotto::findOrFail($id);
    }

    /**
     * Cerca un prodotto partendo dallo slug, che è univoco per tutte le lingue
     * @param $slug
     * @return null
     */
    public function findBySlug($slug)
    {
        $prodotto = Prodotto::whereSlug($slug)
            ->rememberForever("prodotto-{$slug}-".$this->getLingua())
            ->get();

        if($prodotto->isEmpty()) throw new ModelNotFoundException;

        return $prodotto->first();
    }

    /**
     * Preleva gli ultimi prodotti in evidenza
     * @param int $numero
     * @return mixed
     * @todo fix per lingua
     */
    public function ultimiInEvidenza($numero = 4)
    {
        $prodotti = Prodotto::where("in_evidenza", "=", 1)
            ->whereLang($this->getLingua())
            ->orderBy(Prodotto::CREATED_AT)
            ->take($numero)
            ->rememberForever('evidenza-'.$this->getLingua())
            ->get();

        return $prodotti;
    }

    /**
     * Ricerca dal category slug il prodotto con le sue immagini
     * @param $slug
     */
    public function searchByCatSlug($slug)
    {
       $cat = Categoria::whereSlug($slug)->with('prodotto')
           ->get();
       return $cat->isEmpty() ? null : $cat->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id , array $data)
    {
        $slug = isset($data["slug"]) ? $data["slug"] : '';
        $this->clearAllCache($slug);

        if(isset($data["slug_lingua"])) unset($data["slug_lingua"]);
        $prodotto = $this->find($id);

        $prodotto->update($data);

        return $prodotto;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $this->clearAllCache($data["slug"]);

        return Prodotto::create([
                                "codice" => $data["codice"],
                                "nome" => $data["nome"],
                                "slug" => $data["slug"],
                                "slug_lingua" => $data["slug_lingua"] ? $data["slug_lingua"] : $this->generaSlugLingua($data),
                                "lang" => $this->getLingua(),
                                "descrizione" => $data["descrizione"],
                                "descrizione_estesa" => $data["descrizione_estesa"],
                                "in_evidenza" => (boolean)$data["in_evidenza"],
                                "data_variabile" => null,
                                ]);
    }

    /**
     * Delete
     *
     * @param $id
     * @throws ModelNotFoundException
     */
    public function delete($id)
    {
        $prod = $this->find($id);
        return $prod->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlugLingua($slug_lingua)
    {
        $prodotto = Prodotto::whereSlugLingua($slug_lingua)
            ->whereLang($this->getLingua())
            ->get();

        if($prodotto->isEmpty()) throw new ModelNotFoundException;

        return $prodotto->first();
    }

    /**
     * Associa la categoria ad un prodotto con dependency mapping: pulisce tutto e riassocia quella nuova
     * @param $prodotto_id
     * @param $categoria_id
     * @throws ModelNotFoundException
     */
    public function associaCategoria($prodotto_id, $categoria_id)
    {
        $prodotto = $this->find($prodotto_id);
        $cat_ids = [];
        $prodotto->categoria()->get()->each(function($cat) use (&$cat_ids){
            $cat_ids[] = $cat->id;
        });
        $prodotto->categoria()->detach($cat_ids);
        $prodotto->categoria()->attach($categoria_id);
    }

    /**
     * Ottiene tutti gli accessori legati ad un prodotto
     * @param $id
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getAccessori($id)
    {
        //prod
        $prodotto = $this->find($id);
        $accessori_prodotto = $prodotto->accessori()->get()->toArray();
        // cat
        $cat = $prodotto->categoria()->first();
        $accessori_categoria = $cat ? $cat->accessori()->get()->toArray() : [];

        return array_merge($accessori_categoria, $accessori_prodotto);
    }

    /**
     * Associa un accessorio ad un prodotto
     * @param $prodotto_id
     * @param $accessorio_id
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function associaAccessorio($prodotto_id, $accessorio_id)
    {
        $prodotto = $this->find($prodotto_id);
        $accessorio = Accessori::findOrFail($accessorio_id);
        // attach data
        return $prodotto->accessori()->save($accessorio);
    }

    /**
     * Deassocia un accessorio
     * @param $prodotto_id
     * @param $accessorio_id
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deassociaAccessorio($prodotto_id, $accessorio_id)
    {
        $prodotto = $this->find($prodotto_id);
        return $prodotto->accessori()->detach($accessorio_id);
    }

    /**
     * Pulisce tutta la cache dei prodotti
     */
    protected function clearAllCache($slug)
    {
        // prodotti in evidenza
        Cache::forget('evidenza-'.$this->getLingua());
        // prodotto
        Cache::forget("prodotto-{$slug}-".$this->getLingua());

    }

}