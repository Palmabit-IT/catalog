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
use Palmabit\Catalog\Models\CategoryDescription;
use Palmabit\Library\Exceptions\InvalidException;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Multilanguage\Interfaces\MultilinguageRepositoryInterface;
use Palmabit\Multilanguage\Traits\LanguageHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Palmabit\Catalog\Helpers\Helper as ImageHelper;
use DB;

class EloquentCategoryRepository extends EloquentBaseRepository implements MultilinguageRepositoryInterface, TreeInterface
{
    use LanguageHelper;

    /**
     * If the repo is used as admin or not
     * @var Boolean
     */
    protected $is_admin;

    protected $model_description;

    public function __construct($is_admin = false)
    {
        $this->is_admin = $is_admin;
        $this->model_description = new CategoryDescription();
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
        $cats = $this->model_description->whereDescription($description)->get();
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
        $cats = $this->model_description->whereSlug($slug)->get();
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
                                      "name" =>$data["name"],
                                      "order" => isset($data["order"]) ? $data["order"] : 0,
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
                        "image" => ImageHelper::getBinaryData('200', 'image')
                       ]);
    }

    public function getRootNodes()
    {
        return $this->model
                ->where($this->model->getDepthColumnName(),'=',null)
                ->orWhere($this->model->getDepthColumnName(),'=',0)
                ->whereLang($this->getLang())
                ->orderBy('order','DESC')->get();
    }
    
    /**
     * {@inheritdoc}
     * @override
     */
    public function all()
    {
        $cat = $this->model->orderBy('order', 'DESC')
            ->orderBy('depth', 'ASC')
            ->orderBy("name", 'ASC')
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
        $cat= $this->model_description->whereSlugLang($slug_lang)
            ->whereLang($this->getLang())
            ->get();

        if($cat->isEmpty()) throw new NotFoundException;

        return $cat->first();
    }

    public function getArrSelectCat()
    {
        $all_cats = $this->model->orderBy('name','ASC')
                                ->lists('name','id') ;
        $all_cats[""] = "Qualsiasi";

        return $all_cats;
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
     * @todo finish test
     */
    public function setParent($id, $parent_id)
    {
        if(! ($parent_id)) return $this->setRoot($id);

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

    /**
     * @return mixed
     * @todo test
     * @throws \Palmabit\Library\Exceptions\NotFoundException
     */
    public function getSiblians($id)
    {
        $cat = $this->find($id);
        return $cat->getSiblings()->whereLang($this->getLang())->get();
    }

    /**
     * @return mixed
     * @todo test
     * @throws \Palmabit\Library\Exceptions\NotFoundException
     */
    public function getSiblingsAndSelf($id, Array $columns = ['*'] )
    {
        $cat = $this->find($id);
        return $cat->siblingsAndSelf()->whereLang($this->getLang())->get($columns);
    }

    /**
     *
     * @todo test
     * @throws \Palmabit\Library\Exceptions\NotFoundException
     */
    public function hasChildrens($id)
    {
        $cat = $this->find($id);
        return (boolean)$cat->children()->count();
    }

    /**
     * @param $category_id
     * @param $value
     */
    public function setDepth($category_id, $value)
    {
        DB::table('category')
            ->where('id','=', $category_id)
            ->update(["depth"=> $value]);
    }

    public function resizeImage($id, $size)
    {
        $cat = $this->find($id);

        if( is_null($cat->image) ) return;

        $img_data = $cat->getRawImage();
        $resized_image = \Image::raw($img_data)->resize($size, null, true);

        $cat->image = $resized_image;
        $cat->save();
    }

    public function resizeAllImages($size = 200)
    {
        $cats = $this->all();
        foreach($cats as $cat)
        {
            $this->resizeImage($cat->id, $size);
        }

    }
}