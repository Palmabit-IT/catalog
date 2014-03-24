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
use L, Config, DB, Cache, App;

class EloquentProductRepository extends EloquentBaseRepository implements MultilinguageRepositoryInterface
{
    use LanguageHelper;
    /**
     * If the repository is used in admin panel
     * @var Boolean
     */
    protected $is_admin;

    protected static $copy_name = "_copia";

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
     * @param int $max
     * @return null
     * @todo unit test
     */
    public function getOnlyFirstOffersMax($max = 8)
    {
        $products = $this->model->whereLang($this->getLang())
            ->where("offer","1")
            ->orderBy("offer","DESC")
            ->take($max)
            ->get();
        return $products->isEmpty() ? null : $products;
    }

    /**
     * @param int $max
     * @return null
     * @todo unit test
     */
    public function getOnlyFeaturedMax($max = 8)
    {
        $products = $this->model->whereLang($this->getLang())
            ->where("featured","1")
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
     * Get the products in offert
     * @param int $max
     * @return mixed
     */
    public function offertProducts($max = 4)
    {
        $products = $this->model->whereOffer(1)
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

        $product = $this->find($id);

        $this->updateSlugLang($data, $product);

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

    /**
     * Duplicate a product
     * //@todo could extract the logic to another class
     * @param $product_id
     */
    public function duplicate($product_id)
    {
        $product = $this->find($product_id);
        $cloned_product = $this->duplicateProduct($product);
        // duplicate data
        $this->duplicateCategories($product, $cloned_product);
        $this->duplicateImages($product_id, $cloned_product->id);
        $this->duplicateAccessories($product, $cloned_product);

        return $cloned_product;
    }

    /**
     * @param $product_id
     */
    protected function duplicateProduct($product)
    {
        // get data
        $cloned_product = clone($product);
        // prepare data
        unset($cloned_product->slug_lang);
        unset($cloned_product->slug);
        unset($cloned_product->id);
        $cloned_product->exists = false;
        $this->updateProductName($cloned_product);
        // save
        $cloned_product->save();
        // set new temporary slug_lang
        $cloned_product->update(["slug_lang" => $cloned_product->id]);

        return $cloned_product;
    }

    /**
     * @param $product
     * @param $cloned_product
     */
    protected function duplicateCategories($product, $cloned_product)
    {
        // get the cats
        $cat_ids = $product->categories()->get()->lists('id');
        // attach all the cats
        foreach ($cat_ids as $cat_id)
        {
            $this->associateCategory($cloned_product->id, $cat_id);
        }
    }

    /**
     * @param $product
     * @param $cloned_product
     */
    protected function duplicateAccessories($product, $cloned_product)
    {
        // get the accessories
        $acc_ids = $product->accessories()->get()->lists('id');
        // attach all the accessories
        foreach ($acc_ids as $acc_id) {
            $this->attachProduct($cloned_product->id, $acc_id);
        }
    }

    /**
     * @param $product_id
     */
    protected function duplicateImages($product_id, $cloned_product_id)
    {
        $images = App::make('product_image_repository')->getByProductId($product_id);
        // copy them
        foreach ($images as $image) {
            unset($image->id);
            $image->exists     = false;
            $image->product_id = $cloned_product_id;
            $image->save();
        }
    }

    /**
     * @param $cloned_product
     */
    protected function updateProductName($cloned_product)
    {
        // set new name with copy
        $cloned_product->name.= self::$copy_name;
    }

}