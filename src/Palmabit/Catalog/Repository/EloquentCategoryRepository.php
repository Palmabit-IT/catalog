<?php
/**
 * Class EloquentCategoryRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
namespace Palmabit\Catalog\Repository;

use Baum\MoveNotPossibleException;
use Palmabit\Catalog\Interfaces\TreeInterface;
use Palmabit\Catalog\Models\Category;
use Palmabit\Library\Exceptions\InvalidException;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Multilanguage\Interfaces\MultilinguageRepositoryInterface;
use Palmabit\Multilanguage\Traits\LanguageHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Palmabit\Catalog\Helpers\Helper as ImageHelper;

class EloquentCategoryRepository extends EloquentBaseRepository implements MultilinguageRepositoryInterface, TreeInterface
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
        return parent::__construct(new Category);
    }

    /**
     * Search category from description
     *
     * @param $descrizione
     * @return null
     */
    public function search($description)
    {
        $cats = $this->model->whereDescription($description)->get();
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
     * @override
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function create(array $data)
    {
        return $this->model->create(array(
                                      "description" =>$data["description"],
                                      "slug" => $data["slug"],
                                      "slug_lang" => $data["slug_lang"] ? $data["slug_lang"] : $this->generateSlugLang($data),
                                      "lang" => $this->getLang()
                                 ));
    }

    /**
     * @param $id
     * @todo test
     */
    public function updateImage($id)
    {
        $model = $this->find($id);
        $model->update([
                        "image" => ImageHelper::getBinaryData('600', 'image')
                       ]);
    }

    /**
     * {@inheritdoc}
     * @override
     */
    public function all()
    {
        $cat = $this->model->whereLang($this->getLang())
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
        $cat= $this->model->whereSlugLang($slug_lang)
            ->whereLang($this->getLang())
            ->get();

        if($cat->isEmpty()) throw new ModelNotFoundException;

        return $cat->first();
    }


    /**
     * @param $id
     * @todo tests
     */
    public function getParent($id)
    {
        return $this->find($id)->parent()->get();
    }

    /**
     * @param $id
     * @todo tests
     */
    public function getChildrens($id)
    {
        return $this->find($id)->children()->get();
    }

    /**
     * @param $id
     * @param $parent_id
     * @throws \Palmabit\Library\Exceptions\NotFoundException
     */
    public function setParent($id, $parent_id)
    {
        try
        {
            $this->find($id)->makeChildOf($parent_id);
        }catch(MoveNotPossibleException $e)
        {
            throw new InvalidException;
        }
    }

    /**
     * @param $id
     * @param $parent_id
     * @todo tests
     */
    public function setRoot($id)
    {
        $this->find($id)->makeRoot();
    }
}