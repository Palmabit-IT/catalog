<?php namespace Prodotti\Repository;
use Classes\RepositoryInterface;
use Tags;
use DB;

/**
 * Class TagsTepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class TagsRepository implements RepositoryInterface
{
    /**
     * Gets all the objects
     *
     * @return mixed
     */
    public function all()
    {
        return Tags::all();
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
        return Tags::findOrFail($id);
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
        $tag = $this->find($id);

        $tag->update($data);

        return $tag;
    }

    /**
     * Crea un model
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Tags::create($data);
    }

    /**
     * Rimuove la risorsa dal db
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $tag = $this->find($id);
        return $tag->delete();
    }

    /**
     * Ottiene tutti i prodotti distinti per descrizione
     * @return mixed
     */
    public function allDistinct()
    {
        return Tags::groupBy('descrizione')->get(["id", "descrizione"])->toArray();
    }
}