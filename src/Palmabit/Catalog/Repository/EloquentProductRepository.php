<?php namespace Palmabit\Catalog\Repository;

/**
 * Class ProdottoRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\CategoryDescription;
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
     *
     * @var Boolean
     */
    protected $is_admin;
    /**
     * If the repository need to filter update data for non_default language
     *
     * @var bool
     */
    public $general_form_filter = false;

    protected static $copy_name = "_copia";

    public function __construct($is_admin = false, $model = null)
    {
        $this->is_admin = $is_admin;
        $model = $model ? $model : new Product;
        return parent::__construct($model);
    }

    /**
     * @override
     * @param array $input_filter
     * @return mixed|void
     */
    public function all(array $input_filter = null)
    {
        $results_per_page = Config::get('catalog::admin_per_page');
        list($products_table, $product_category_table, $category_table) = $this->setupTableNames();

        $q = DB::table($products_table)
               ->leftJoin($product_category_table, $products_table . '.id', '=', $product_category_table . '.product_id')
               ->leftJoin($category_table, $category_table . '.id', '=', $product_category_table . '.category_id')
                // language check
               ->where($products_table . '.lang', '=', $this->getLang())
                // ordering
               ->orderBy($products_table . ".order", "DESC")
               ->orderBy($products_table . ".name", "ASC")
                // get only a line per product
               ->groupBy($products_table . '.id');

        $q = $this->applySearchFilters($input_filter, $products_table, $category_table, $q);

        $q = $this->createAllSelect($q, $products_table, $category_table);

        return $q->paginate($results_per_page);
    }

    /**
     * @param array $input_filter
     * @param       $q
     * @return mixed
     */
    protected function applySearchFilters(array $input_filter = null, $products_table, $category_table, $q)
    {
        if($input_filter)
        {
            foreach($input_filter as $column => $value)
            {
                if($value !== '')
                {
                    switch($column)
                    {
                        case 'code':
                            $q = $q->where($products_table . '.code', '=', $value);
                            break;
                        case 'name':
                            $q = $q->where($products_table . '.name', 'LIKE', "%{$value}%");
                            break;
                        case 'featured':
                            $q = $q->where($products_table . '.featured', '=', "{$value}");
                            break;
                        case 'public':
                            $q = $q->where($products_table . '.public', '=', "{$value}");
                            break;
                        case 'offer':
                            $q = $q->where($products_table . '.offer', '=', $value);
                            break;
                        case 'professional':
                            $q = $q->where($products_table . '.professional', '=', $value);
                            break;
                        case 'category_id':
                            $q = $q->where($category_table . '.id', '=', $value);
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return $q;
    }

    /**
     * @param $q
     * @param $user_table_name
     * @param $profile_table_name
     * @return mixed
     */
    protected function createAllSelect($q, $products_table, $category_table)
    {
        $q = $q->select([$products_table . '.*', $category_table . 'name' => 'description']);

        return $q;
    }

    public function getFirstOffersMax($max = 8)
    {
        $products = $this->model->whereLang($this->getLang())
                                ->orderBy("offer", "DESC")
                                ->where('public', '=', '1')
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
                                ->where("offer", '=', "1")
                                ->where('public', '=', '1')
                                ->orderBy("offer", "DESC")
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
                                ->where("featured", "1")
                                ->orderBy("offer", "DESC")
                                ->where('public', '=', '1')
                                ->take($max)
                                ->get();
        return $products->isEmpty() ? null : $products;
    }

    /**
     * Finds a product starting from the slug
     *
     * @param $slug
     * @return null
     */
    public function findBySlug($slug)
    {
        $product = $this->model->whereSlug($slug)
                               ->where('public', '=', '1')
                               ->remember(1200, "product-{$slug}-" . $this->getLang())
                               ->get();
        if($product->isEmpty()) throw new ModelNotFoundException;

        return $product->first();
    }

    /**
     * Get the featured products
     *
     * @param int $max
     * @return mixed
     */
    public function featuredProducts($max = 4)
    {
        $products = $this->model->whereFeatured(1)
                                ->where('public', '=', '1')
                                ->whereLang($this->getLang())
                                ->orderBy($this->model->getCreatedAtColumn())
                                ->take($max)
                                ->remember(1200, 'featured-' . $this->getLang())
                                ->get();

        return $products;
    }

    /**
     * Get the products in offer
     *
     * @param int $max
     * @return mixed
     */
    public function offertProducts($max = 4)
    {
        $products = $this->model->whereOffer(1)
                                ->whereLang($this->getLang())
                                ->where('public', '=', '1')
                                ->orderBy($this->model->getCreatedAtColumn())
                                ->take($max)
                                ->remember(1200, 'offer-' . $this->getLang())
                                ->get();

        return $products;
    }

    /**
     * Find category by slug
     *
     * @param $slug
     */
    public function searchByCatSlug($slug)
    {
        $cat = CategoryDescription::whereSlug($slug)->with('product')
                       ->orderBy('order', 'name')
                       ->get();
        return $cat->isEmpty() ? null : $cat->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $this->clearEmptySlug($data);
        $data = $this->clearEmptyInput($data);
        $data = $this->removeIdData($data);

        $product = $this->find($id);
        $this->resetProductCache($product);

        if($this->isUpdateBulkData())
        {
            $this->updateBulkProducts($data, $product);
        } else
        {
            $this->updateSingleProduct($data, $product);
        }

        return $product;
    }

    /**
     * @return array
     */
    protected function setupTableNames()
    {
        $products_table = 'product';
        $product_category_table = 'product_category';
        $category_table = 'category';

        return array($products_table, $product_category_table, $category_table);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {

        $product = $this->model->create([
                                                "code"                                       => $data["code"],
                                                "name"                                       => $data["name"],
                                                "slug"                                       => $data["slug"],
                                                "slug_lang"                                  => $data["slug_lang"] ? $data["slug_lang"] : $this->generateSlugLang($data),
                                                "lang"                                       => $this->getLang(),
                                                "description"                                => $data["description"],
                                                "long_description"                           => $data["long_description"],
                                                "featured"                                   => (boolean)$data["featured"],
                                                "public"                                     => (boolean)$data["public"],
                                                "offer"                                      => (boolean)$data["offer"],
                                                "stock"                                      => (boolean)$data["stock"],
                                                "with_vat"                                   => (boolean)$data["with_vat"],
                                                "video_link"                                 => isset($data["video_link"]) ? $data["video_link"] : null,
                                                "professional"                               => (boolean)$data["professional"],
                                                "price1"                                     => isset($data["price1"]) ? $data["price1"] : null,
                                                "price2"                                     => isset($data["price2"]) ? $data["price2"] : null,
                                                "price3"                                     => isset($data["price3"]) ? $data["price3"] : null,
                                                "price4"                                     => isset($data["price4"]) ? $data["price4"] : null,
                                                'quantity_pricing_enabled'                   => (boolean)$data['quantity_pricing_enabled'],
                                                'quantity_pricing_quantity'                  => (!empty($data['quantity_pricing_quantity'])) ? $data['quantity_pricing_quantity'] : 0,
                                                'quantity_pricing_quantity_non_professional' => (!empty($data['quantity_pricing_quantity_non_professional'])) ? $data['quantity_pricing_quantity_non_professional'] : 0
                                        ]);
        $this->resetProductCache($product);
        return $product;
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
        $this->resetProductCache($prod);
        return $prod->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlugLang($slug_lang)
    {
        $query = $this->model->whereSlugLang($slug_lang)
                             ->orderBy('order', 'DESC')
                             ->orderBy('name', 'ASC')
                             ->whereLang($this->getLang());
        $product = $query->get();

        if($product->isEmpty()) throw new NotFoundException;

        return $product->first();
    }

    /**
     * Associates the cateogry to a product with dependency mapping pattern
     *
     * @param $product_id
     * @param $category_id
     * @throws ModelNotFoundException
     */
    public function associateCategory($product_id, $category_id)
    {
        try
        {
            $product = $this->find($product_id);
        } catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }
        Event::fire('repository.products.attachCategory', [$product_id, $category_id]);
        $product->categories()->attach($category_id);
    }

    /**
     * Deassociate a category to a given product
     *
     * @param $product_id
     * @param $category_id
     */
    public function deassociateCategory($product_id, $category_id)
    {
        try
        {
            $product = $this->find($product_id);
        } catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }

        $product->categories()->detach($category_id);
    }

    /**
     * Attach a product to another
     *
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
     *
     * @param $fist_product_id
     * @param $second_product_id
     */
    public function detachProduct($first_product_id, $second_product_id)
    {
        $first_product = $this->model->find($first_product_id);
        $first_product->accessories()->detach($second_product_id);
    }

    /**
     * Obtains all product accessories
     *
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
     *
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

    public function findByCodeAndLang($code, $lang)
    {
        return $this->model
                ->whereCode($code)
                ->whereLang($lang)
                ->firstOrFail();
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
        foreach($cat_ids as $cat_id)
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
        foreach($acc_ids as $acc_id)
        {
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
        foreach($images as $image)
        {
            unset($image->id);
            $image->exists = false;
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
        $cloned_product->name .= self::$copy_name;
    }

    public function getProductLangsAvailable($slug_lang)
    {
        return $this->model->whereSlugLang($slug_lang)->get([
                                                                    "id",
                                                                    "code",
                                                                    "name",
                                                                    "slug",
                                                                    "lang",
                                                                    "slug_lang",
                                                            ]);
    }

    public function enableGeneralFormFilter()
    {
        return $this->setGeneralFormFilter(true);
    }

    public function disableGeneralFormFilter()
    {
        return $this->setGeneralFormFilter(false);
    }

    public function deleteFromSlugLang($slug_lang)
    {
        return $this->model->whereSlugLang($slug_lang)->delete();
    }

    /**
     * @return $this
     */
    protected function setGeneralFormFilter($value)
    {
        $this->model->setGeneralFormFilterEnabled($value);
        $this->general_form_filter = $value;

        return $this;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function clearEmptySlug(array $data)
    {
        return isset($data["slug"]) ? $data["slug"] : '';
    }

    /**
     * @param array $data
     * @return array
     */
    protected function clearEmptyInput(array $data)
    {
        $data['quantity_pricing_quantity'] = (!empty($data['quantity_pricing_quantity'])) ? $data['quantity_pricing_quantity'] : 0;
        $data['quantity_pricing_quantity_non_professional'] =
                (!empty($data['quantity_pricing_quantity_non_professional'])) ? $data['quantity_pricing_quantity_non_professional'] : 0;
        return $data;
    }

    /**
     * @return bool
     */
    protected function isUpdateBulkData()
    {
        return $this->general_form_filter&&$this->getLang() == L::getDefault();
    }

    /**
     * @param array $data
     * @return array
     */
    protected function removeLangData(array $data)
    {
        if(isset($data['lang'])) unset($data['lang']);
        return $data;
        return $data;
    }

    /**
     * @param array $data
     * @param       $product
     */
    protected function updateSingleProduct(array $data, $product)
    {
        $this->updateSlugLang($data, $product);
        if($this->general_form_filter) $product->setGeneralFormFilterEnabled(true);
        $product->update($data);
    }

    /**
     * @param array $data
     * @param       $product
     */
    protected function updateBulkProducts(array $data, $product)
    {
        $data = $this->removeLangData($data);
        $products = $this->model->whereSlugLang($product->slug_lang)->get();
        foreach($products as $product)
        {
            $update_data = array_only($data, $product->getUniqueData());
            $this->updateSlugLang($data, $product);
            $product->update($update_data);

            $this->resetProductCache($product);
        }
    }

    /**
     * @param $product
     */
    protected function resetProductCache($product)
    {
        Cache::forget('featured-' . $product->lang);
        Cache::forget("offer-{$product->slug}-" . $product->lang);
        Cache::forget("product-{$product->slug}-" . $product->lang);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function removeIdData(array $data)
    {
        if(isset($data["id"])) unset($data["id"]);return $data;
        return $data;
    }
}