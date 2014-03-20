<?php namespace Palmabit\Catalog\Repository;
/**
 * Class ProdottoRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\Product;
use Palmabit\Multilanguage\Interfaces\MultilinguageRepositoryInterface;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Multilanguage\Traits\LanguageHelper;
use Palmabit\Library\Exceptions\NotFoundException;
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

    public function __construct($is_admin = false, $model = null)
    {
        $this->is_admin = $is_admin;
        $model = $model ? $model : new Product;
        return parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $per_page = Config::get('catalog::admin_per_page');
        set_view_paginator('pagination::slider-3');
        $products = $this->model->whereLang($this->getLang())
            ->orderBy("order","name")
            ->paginate($per_page);
        return $products->isEmpty() ? null : $products;
    }

    public function getFirstOffersMax($max = 8)
    {
        $products = $this->model->whereLang($this->getLang())
            ->orderBy("offer","DESC")
            ->take($max)
            ->get();
        return $products->isEmpty() ? null : $products;
    }

    /**
     * Finds a product starting from the slug
     * @param $slug
     * @return null
     */
    public function findBySlug($slug)
    {
        $product = $this->model->whereSlug($slug)
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
        $products = $this->model->whereFeatured(1)
            ->whereLang($this->getLang())
            ->orderBy($this->model->getCreatedAtColumn())
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
        $data['quantity_pricing_quantity'] = (! empty($data['quantity_pricing_quantity'])) ? $data['quantity_pricing_quantity'] : 0;
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

        return $this->model->create([
                                    "code" => $data["code"],
                                    "name" => $data["name"],
                                    "slug" => $data["slug"],
                                    "slug_lang" => $data["slug_lang"] ? $data["slug_lang"] : $this->generateSlugLang($data),
                                    "lang" => $this->getLang(),
                                    "description" => $data["description"],
                                    "long_description" => $data["long_description"],
                                    "featured" => (boolean)$data["featured"],
                                    "public" => (boolean)$data["public"],
                                    "offer" => (boolean)$data["offer"],
                                    "stock" => (boolean)$data["stock"],
                                    "with_vat" => (boolean)$data["with_vat"],
                                    "video_link" => isset($data["video_link"]) ? $data["video_link"] : null,
                                    "professional" => (boolean)$data["professional"],
                                    "price1" => $data["price1"],
                                    "price2" => $data["price2"],
                                    "price3" => $data["price3"],
                                    "price4" => $data["price4"],
                                    'quantity_pricing_enabled' => (boolean)$data['quantity_pricing_enabled'],
                                    'quantity_pricing_quantity' => (! empty($data['quantity_pricing_quantity'])) ? $data['quantity_pricing_quantity'] : 0
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
        $product = $this->model->whereSlugLang($slug_lang)
            ->whereLang($this->getLang())
            ->get();

        if($product->isEmpty()) throw new NotFoundException;

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
        try
        {
            $product = $this->find($product_id);
        }
        catch(ModelNotFoundException $e)
        {
        throw new NotFoundException;
        }
        Event::fire('repository.products.attachCategory', [$product_id, $category_id]);
        $product->categories()->attach($category_id);
    }

    /**
     * Deassociate a category to a given product
     * @param $product_id
     * @param $category_id
     */
    public function deassociateCategory($product_id, $category_id)
    {
        try
        {
            $product = $this->find($product_id);
        }
        catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }

        $product->categories()->detach($category_id);
    }

    /**
     * Attach a product to another
     * @param $first_product_id
     * @param $second_product_id
     * @throws \Palmabit\Library\Exceptions\NotFoundException
     */
    public function attachProduct($first_product_id, $second_product_id)
    {
        Event::fire('repository.products.attachProduct', [$first_product_id, $second_product_id]);
        $first_product = $this->model->find($first_product_id);
        $first_product->accessories()->attach($second_product_id);
    }

    /**
     * Detach a product
     * @param $fist_product_id
     * @param $second_product_id
     */
    public function detachProduct($first_product_id, $second_product_id)
    {
        $first_product = $this->model->find($first_product_id);
        $first_product->accessories()->detach($second_product_id);
    }

    /**
     * Clean product cache
     */
    protected function clearAllCache($slug)
    {
        // prodotti in evidenza
        Cache::forget('featured-'.$this->getLang());
        // prodotto
        Cache::forget("product-{$slug}-".$this->getLang());
    }

    /**
     * Obtains all product accessories
     * @throws \Palmabit\Library\Exceptions\NotFoundException
     */
    public function getAccessories($id)
    {
        $model = $this->find($id);
        return $model->accessories()->get();
    }

}