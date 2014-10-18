<?php namespace Palmabit\Catalog\Repository;

/**
 * Class ProdottoRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Palmabit\Catalog\Models\CategoryDescription;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Models\ProductDescription;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Library\Exceptions\NotFoundException;
use L, Config, DB, Cache, App;
use Palmabit\Multilanguage\Classes\Traits\LanguageHelperTrait;

class EloquentProductRepository extends EloquentBaseRepository
{
    use LanguageHelperTrait;

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

    protected $model_description;

    public function __construct($is_admin = false, $model = null)
    {
        $this->is_admin = $is_admin;
        $model = $model ? $model : new Product;
        $this->model_description = new ProductDescription();
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
        list($products_table, $product_category_table, $category_table, $product_descriptions_table) = $this->setupTableNames();

        $q = DB::table($products_table)
               ->leftJoin($product_category_table, $products_table . '.id', '=', $product_category_table . '.product_id')
               ->leftJoin($category_table, $category_table . '.id', '=', $product_category_table . '.category_id')
               ->leftJoin($product_descriptions_table, $products_table . '.id', '=', $product_descriptions_table . '.product_id')
                // language check
               ->where($product_descriptions_table . '.lang', '=', $this->getLang())
                // ordering
               ->orderBy($products_table . ".order", "DESC")
               ->orderBy($product_descriptions_table . ".name", "ASC")
                // get only a line per product
               ->groupBy($products_table . '.id');

        $q = $this->applySearchFilters($input_filter, $products_table, $category_table, $product_descriptions_table, $q);

        $q = $this->createAllSelect($q, $products_table, $category_table, $product_descriptions_table);

        return $q->paginate($results_per_page);
    }

    /**
     * @param array $input_filter
     * @param       $q
     * @return mixed
     */
    protected function applySearchFilters(array $input_filter = null, $products_table, $category_table, $products_description_table, $q)
    {
        if($input_filter)
        {
            foreach($input_filter as $column => $value)
            {
                if($value !== '')
                {
                    switch($column)
                    {
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
                        case 'code':
                            $q = $q->where($products_table . '.code', '=', $value);
                            break;
                        case 'name':
                            $q = $q->where($products_description_table . '.name', 'LIKE', "%{$value}%");
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
    protected function createAllSelect($q, $products_table, $category_table, $products_description_table)
    {
        $q = $q->select([
                                $products_table . '.*',
                                $category_table . '.name' => 'description',
                                $products_description_table . '.product_id',
                                $products_description_table . '.name',
                                $products_description_table . '.slug',
                                $products_description_table . '.description',
                                $products_description_table . '.long_description',
                                $products_description_table . '.lang',
                        ]);

        return $q;
    }

    public function getFirstOffersMax($max = 8)
    {
        $products = $this->model->orderBy("offer", "DESC")
                                ->where('public', '=', '1')
                                ->take($max)
                                ->get();
        return $products->isEmpty() ? null : $products;
    }

    /**
     * @param int $max
     * @return null
     */
    public function getOnlyFirstOffersMax($max = 8)
    {
        $products = $this->model->where("offer", '=', "1")
                                ->where('public', '=', '1')
                                ->orderBy("offer", "DESC")
                                ->take($max)
                                ->get();
        return $products->isEmpty() ? null : $products;
    }

    /**
     * @param int $max
     * @return null
     */
    public function getOnlyFeaturedMax($max = 8)
    {
        $products = $this->model->where("featured", "1")
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
        $product_description = $this->model_description
                ->whereSlug($slug)
                ->get();

        if($product_description->isEmpty()) throw new ModelNotFoundException;

        return $product_description->first()->product()->first();
    }

    /**
     * Get the featured products
     *
     * @param int $max
     * @return mixed
     */
    public function featuredProducts($max = 4)
    {
        return $this->model->whereFeatured(1)
                           ->wherePublic(1)
                           ->orderBy($this->model->getCreatedAtColumn())
                           ->take($max)
                           ->get();
    }

    /**
     * Get the products in offer
     *
     * @param int $max
     * @return mixed
     */
    public function offerProducts($max = 4)
    {
        $products = $this->model->whereOffer(1)
                                ->where('public', '=', '1')
                                ->orderBy($this->model->getCreatedAtColumn())
                                ->take($max)
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
        $data = $this->clearEmptyInput($data);
        $data = $this->removeIdData($data);

        $product = $this->find($id)->decorateLanguage($this->getLang())->fill($data);
        $product->save();

        return $product->getResource();
    }

    /**
     * @return array
     */
    protected function setupTableNames()
    {
        $products_table = 'product';
        $product_category_table = 'product_category';
        $category_table = 'category';
        $product_descriptions_table = 'product_description';

        return array($products_table, $product_category_table, $category_table, $product_descriptions_table);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $product_data = [
                "code"                                       => $data["code"],
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
        ];

        $description_data = [
                "name"             => $data["name"],
                "slug"             => $data["slug"],
                "description"      => $data["description"],
                "long_description" => $data["long_description"]
        ];

        $product = $this->model->create($product_data);
        $product->decorateLanguage($this->getLang())->fill($description_data)->save();

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
        return $this->find($id)->delete();
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
     *
     * @param $product_id
     */
    public function duplicate($product_id)
    {
        $product = $this->find($product_id);
        $cloned_product = $this->duplicateProduct($product);
        $this->duplicateDescriptions($product, $cloned_product);
        $this->duplicateAccessories($product, $cloned_product);
        $this->duplicateCategories($product, $cloned_product);
        $this->duplicateImages($product, $cloned_product);
        return $cloned_product;
    }

    public function findByCodeAndLang($code)
    {
        return $this->model->whereCode($code)
                           ->firstOrFail();
    }

    /**
     * @param $product_id
     */
    protected function duplicateProduct($product)
    {
        $cloned_product = clone($product);
        // override data
        unset($cloned_product->id);
        $cloned_product->exists = false;
        $cloned_product->code .= static::$copy_name;
        // save
        $cloned_product->save();

        return $cloned_product;
    }

    /**
     * @param $product
     * @param $cloned_product
     */
    private function duplicateDescriptions($product, $cloned_product)
    {
        foreach($product->descriptions()->get() as $description)
        {
            $description->product_id = $cloned_product->id;
            $description->slug .= "_" . rand(1000, 1000000);
            $description->name .= static::$copy_name;
            unset($description->id);
            $description->exists = false;
            $description->save();
        }
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
    protected function duplicateImages($product, $cloned_product)
    {
        $images = App::make('product_image_repository')->getByProductId($product->id);
        // copy them
        foreach ($images as $image) {
            unset($image->id);
            $image->exists     = false;
            $image->product_id = $cloned_product->id;
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

    public function getProductLangsAvailable($id)
    {
        return $this->model_description->with('product')->whereProductId($id)->get();
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
     * @param array $data
     * @return array
     */
    protected function removeLangData(array $data)
    {
        if(isset($data['lang'])) unset($data['lang']);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function removeIdData(array $data)
    {
        if(isset($data["id"])) unset($data["id"]);

        return $data;
    }

    protected function getLang()
    {
      return L::get();
    }
}