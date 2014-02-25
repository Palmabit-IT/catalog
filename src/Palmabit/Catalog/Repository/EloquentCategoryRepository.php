<?php
/**
 * Class EloquentCategoryRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
namespace Palmabit\Catalog\Repository;

use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Multilanguage\Interfaces\MultilinguageRepositoryInterface;
use Palmabit\Multilanguage\Traits\LanguageHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentCategoryRepository extends EloquentBaseRepository implements MultilinguageRepositoryInterface
{
    use LanguageHelper;

    /**
     * If the repo is used as admin or not
     * @var Boolean
     */
    protected $is_admin;

    protected $model_name = 'Palmabit\Catalog\Models\Category';

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
        $model = $this->model_name;
        $cats = $model::whereDescription($description)->get();
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
        $model = $this->model_name;
        $cats = $model::whereSlug($slug)->get();
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
        $model = $this->model_name;
        return $model::create(array(
                                      "description" =>$data["description"],
                                      "slug" => $data["slug"],
                                      "slug_lang" => $data["slug_lang"] ? $data["slug_lang"] : $this->generateSlugLang($data),
                                      "lang" => $this->getLang()
                                 ));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $model = $this->model_name;
        $cat = $model::whereLang($this->getLang())
            ->orderBy("description")
            ->get();

        return $cat->isEmpty() ? null : $cat->all();
    }

    /**
     * {@inheritdoc}
     *
     * @param $slug_lang
     * @return mixed
     */
    public function findBySlugLang($slug_lang)
    {
        $model = $this->model_name;
        $cat= $model::whereSlugLang($slug_lang)
            ->whereLang($this->getLang())
            ->get();

        if($cat->isEmpty()) throw new ModelNotFoundException;

        return $cat->first();
    }

}