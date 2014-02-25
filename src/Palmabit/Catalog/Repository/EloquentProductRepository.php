<?php namespace Palmabit\Catalog\Repository;
/**
 * Class ProdottoRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Palmabit\Catalog\Models\Category;
use Palmabit\Multilanguage\Interfaces\MultilinguageRepositoryInterface;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Multilanguage\Traits\LanguageHelper;
use L;
use Config;
use DB;
use Cache;

class EloquentProductRepository extends EloquentBaseRepository implements MultilinguageRepositoryInterface
{
    use LanguageHelper;
    /**
     * If the repository is used in admin panel
     * @var Boolean
     */
    protected $is_admin;
    /**
     * The name of the model
     * @var
     */
    protected $model_name = 'Palmabit\Catalog\Models\Product';

    public function __construct($is_admin = false)
    {
        $this->is_admin = $is_admin;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $per_page = Config::get('catalog::admin_per_page');
        set_view_paginator('pagination::slider-3');
        $model = $this->model_name;
        $products = $model::whereLang($this->getLang())
            ->orderBy("order","name")
            ->paginate($per_page);
        return $products->isEmpty() ? null : $products;
    }

    /**
     * Finds a product starting from the slug
     * @param $slug
     * @return null
     */
    public function findBySlug($slug)
    {
        $model = $this->model_name;
        $product = $model::whereSlug($slug)
            ->rememberForever("product-{$slug}-".$this->getLang())
            ->get();

        if($product->isEmpty()) throw new ModelNotFoundException;

        return $product->first();
    }

    /**
     * Get the featured products
     * @param int $max
     * @return mixed
     */
    public function featuredProducts($max = 4)
    {
        $model = $this->model_name;
        $products = $model::whereFeatured(1)
            ->whereLang($this->getLang())
            ->orderBy($model::CREATED_AT)
            ->take($max)
            ->rememberForever('featured-'.$this->getLang())
            ->get();

        return $products;
    }

    /**
     * Find category by slug
     * @param $slug
     */
    public function searchByCatSlug($slug)
    {
       $cat = Category::whereSlug($slug)->with('product')
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

        if(isset($data["slug_lang"])) unset($data["slug_lang"]);
        $product = $this->find($id);

        $product->update($data);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $this->clearAllCache($data["slug"]);

        $model = $this->model_name;
        return $model::create([
                                    "code" => $data["code"],
                                    "name" => $data["name"],
                                    "slug" => $data["slug"],
                                    "slug_lang" => $data["slug_lang"] ? $data["slug_lang"] : $this->generateSlugLang($data),
                                    "lang" => $this->getLang(),
                                    "description" => $data["description"],
                                    "description_long" => $data["description_long"],
                                    "featured" => (boolean)$data["featured"],
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
        $this->clearAllCache($prod["slug"]);

        return $prod->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlugLang($slug_lang)
    {
        $model = $this->model_name;
        $product = $model::whereSlugLang($slug_lang)
            ->whereLang($this->getLang())
            ->get();

        if($product->isEmpty()) throw new ModelNotFoundException;

        return $product->first();
    }

    /**
     * Associates the cateogry to a product with dependency mapping pattern
     * @param $product_id
     * @param $category_id
     * @throws ModelNotFoundException
     */
    public function associateCategory($product_id, $category_id)
    {
        $product = $this->find($product_id);
        $cat_ids = [];
        $product->categories()->get()->each(function($cat) use (&$cat_ids){
            $cat_ids[] = $cat->id;
        });
        $product->categories()->detach($cat_ids);
        $product->categories()->attach($category_id);
    }

    /**
     * Pulisce tutta la cache dei prodotti
     */
    protected function clearAllCache($slug)
    {
        // prodotti in evidenza
        Cache::forget('featured-'.$this->getLang());
        // prodotto
        Cache::forget("product-{$slug}-".$this->getLang());
    }

}