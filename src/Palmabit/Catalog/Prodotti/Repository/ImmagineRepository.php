<?php namespace Prodotti\Repository;
/**
 * Class ImmagineRepository
 *
 * @author jacopo beschi
 */

use Classes\RepositoryInterface;
use ImmaginiProdotto;
use Prodotti\Helper\ImageHelper;
use DB;
use Image;
use Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ImmagineRepository implements RepositoryInterface{

    /**
     * Gets all the objects
     *
     * @return mixed
     */
    public function all()
    {
        //@todo
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
        return ImmaginiProdotto::findOrFail($id);
    }

    /**
     * Aggiorna un model
     *
     * @param       $id
     * @param array $data
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($id, array $data)
    {
        $immagine = $this->find($id);

        $immagine->update($data);

        return $immagine;
    }

    /**
     * Crea un model
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return ImmaginiProdotto::create([
                                            "descrizione" => $data["descrizione"],
                                            "prodotto_id" => $data["prodotto_id"],
                                            "in_evidenza" => $data["in_evidenza"],
                                            "data" => $this->getBinaryData()
                                        ]);
    }

    /**
     * Preleva l'immagine da un'url
     */
    protected function getBinaryData()
    {
        return Image::make(ImageHelper::getPathFromInput('immagine'))->resize(600, null, true);
    }

    /**
     * Rimuove la risorsa dal db
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $img = $this->find($id);
        return $img->delete();
    }

    /**
     * Cambia l'immagine in evidenza
     *
     * @param $id
     * @param $prodotto_id
     */
    public function cambiaEvidenza($id, $prodotto_id)
    {
        DB::connection()->getPdo()->beginTransaction();
        try
        {
            //rimuove vecchie in evidenza
            ImmaginiProdotto::where('prodotto_id','=',$prodotto_id)
                ->get()
                ->each(function($img){
                    $this->update($img->id, ["in_evidenza" => 0]);
                });
            // setta in evidenza
            $this->update($id, ["in_evidenza" => 1]);
        }
        catch(ModelNotFoundException $e)
        {
            DB::connection()->getPdo()->rollBack();
            throw new NotFoundException("Prodotto non trovato.");
        }
        DB::connection()->getPdo()->commit();
    }
}