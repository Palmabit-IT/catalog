<?php namespace Palmabit\Catalog\Presenters;
/**
 * Class PresenterProducts
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
namespace Presenters;

use ImmaginiProdotto;
use Palmabit\Catalog\Traits\ViewHelper;

class PresenterProducts extends AbstractPresenter {
use ViewHelper;

    protected $default_img_path;

    public function __construct($resource)
    {
        //@todo fix path
        $this->default_img_path = public_path()."/admin/img/no-photo.png";
        return parent::__construct($resource);
    }
    /**
     * Ottiene l'immagine in evidenza
     * @return null
     * @return String|null
     * @todo ritornare un'immagine di default quando non c'è l'img
     */
    public function immagine_evidenza()
    {
        $in_evidenza = $this->resource->immagini_prodotto()
            ->where("in_evidenza","=",1)
            ->get();

        if($in_evidenza->isEmpty())
            return ["data" => "data:image;base64,".base64_encode(ImmaginiProdotto::getImageFromUrl($this->default_img_path) ), "alt" => ""];
        else $in_evidenza = $in_evidenza->first();

        return array("data"=> $in_evidenza ? "data:image;base64,{$in_evidenza->data}" : null, "alt" => $in_evidenza->descrizione);
    }

    public function immagini_all()
    {
        return $this->immagini(false);
    }

    public function immagini_esclusco_evidenza()
    {
        return $this->immagini(true);
    }

    /**
     * Ottene le immagini del prodotto
     * @param $escludi_evidenza
     * @return String
     */
    protected function immagini($escludi_evidenza = true)
    {
        $immagini_a = array();

        $immagini = $this->resource->immagini_prodotto();
        if($escludi_evidenza)
            $immagini = $immagini->where('in_evidenza','=',0);
        $immagini = $immagini->get();

        $immagini->each(function($immagine) use(&$immagini_a){
            $immagini_a[] = [
                "data" => "data:image;base64,{$immagine->data}",
                "alt" => $immagine->descrizione,
                "id" => $immagine->id,
                "in_evidenza" => $immagine->in_evidenza
            ];
        });

        return $immagini_a;
    }

    public function categoria()
    {
        $cat = $this->resource->categoria()->get();
        if(! $cat->isEmpty()) $cat = $cat->first();

        return (isset($cat->descrizione)) ? $cat->descrizione : '';
    }

    public function categoria_id()
    {
        $cat = $this->resource->categoria()->get();
        if(! $cat->isEmpty()) $cat = $cat->first();

        return (isset($cat->id)) ? $cat->id: '';
    }

    /**
     * Ottiene i tags associati al prodotto
     */
    public function tags()
    {
        $tags = $this->resource->tags()->get(["id", "descrizione", "prodotto_id"]);
        return $tags->isEmpty() ? '' : $tags->toArray();
    }

    /**
     * Ottiene tutti i tags distinct
     * @return array
     */
    public function tags_select()
    {
        $tags_repo = new TagsRepository();
        $tags = $tags_repo->allDistinct();
        $tags_select = [];
        foreach($tags as $tag)
        {
            $tags_select[$tag["descrizione"]] = $tag["descrizione"];
        }

        return $tags_select;
    }

    /**
     * Ottiene gli accessori legati alla categoria di appartenenza
     * @return array
     */
    public function accessori_categoria()
    {
        $prodotto = $this->resource;
        $cat = $prodotto->categoria()->first();
        return $cat ? $cat->accessori()->get()->toArray() : [];
    }

} 