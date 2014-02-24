<?php
/**
 * Class CategoryRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
namespace Palmabit\Catalog\Repository;

use Palmabit\Catalog\Models\Category;
use Palmabit\Library\Repository\Interfaces\BaseRepositoryInterface;
use Palmabit\Multilanguage\Interfaces\MultilinguaRepositoryInterface;
use Palmabit\Multilanguage\Traits\LanguageHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository implements RepositoryInterface, MultilinguaRepositoryInterface
{
    use LanguageHelper;

    /**
     * If the repo is used as admin or not
     * @var Boolean
     */
    protected $is_admin;

    public function __construct($is_admin = false)
    {
        $this->is_admin = $is_admin;
    }

    /**
     * Search category from description
     *
     * @param $descrizione
     * @return null
     */
    public function search($description)
    {
        $cats = Category::whereDescription($description)->get();
        return $cats->isEmpty() ? null : $cats->all();
    }

    /**
     * Find category by slug
     *
     * @param $slug
     * @return null
     */
    public function searchBySlug($slug)
    {
        $cats = Category::whereSlug($slug)->get();
        return $cats->isEmpty() ? null : $cats->first();
    }


    /**
     * Create record
     *
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function create(array $data)
    {
        return Category::create(array(
                                      "description" =>$data["description"],
                                      "slug" => $data["slug"],
                                      "slug_lang" => $data["slug_lang"] ? $data["slug_lang"] : $this->generateSlugLand($data),
                                      "lang" => $this->getLang()
                                 ));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $cat = Category::whereLang($this->getLingua())
            ->orderBy("description")
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
        return Category::findOrFail($id);
    }

    /**
     * Update record
     *
     * @param $id
     * @param $descrizione
     * @throws ModelNotFoundException
     */
    public function update($id, array $data)
    {
        $cat = $this->find($id);

        $cat->update(array(
                          "description" => $data["description"],
                          "slug" => $data["slug"]
                     ));

        return $cat;
    }

    /**
     * Delete record
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
     * {@inheritdoc}
     *
     * @param $slug_lang
     * @return mixed
     */
    public function findBySlugLang($slug_lang)
    {
        $cat= Category::whereSlugLang($slug_lang)
            ->whereLang($this->getLang())
            ->get();

        if($cat->isEmpty()) throw new ModelNotFoundException;

        return $cat->first();
    }

}