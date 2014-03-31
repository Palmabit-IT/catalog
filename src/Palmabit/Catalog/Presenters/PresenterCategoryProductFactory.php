<?php  namespace Palmabit\Catalog\Presenters;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Palmabit\Catalog\Models\Category;
use Palmabit\Library\Presenters\PresenterPagination;
use Config;

/**
 * Class PresenterCategoryProductFactory
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class PresenterCategoryProductFactory 
{
    public function __construct()
    {
        $this->r = App::make('category_repository');
    }

    /**
     * @param Category $category
     * @return Collection|PresenterPagination
     * @todo unit test
     */
    public function create(Category $category)
    {
        $per_page = Config::get('catalog::client_per_page');
        // if has childrens returns the subcategories
        if($this->r->hasChildrens($category->id))
        {
            $categories = ($category->children) ? $category->children()
                ->orderBy('description')
                ->paginate($per_page) : array();
            return ( ! empty($categories) ) ? new PresenterPagination('Palmabit\Catalog\Presenters\PresenterCategory', $categories ) : new Collection();
        }
        else
        {
            $products = ($category->products) ? $category->products()
                ->orderBy('order','DESC')
                ->orderBy('name','ASC')
                ->paginate($per_page) : array();
            return ( ! empty($products) ) ? new PresenterPagination('Palmabit\Catalog\Presenters\PresenterProducts', $products ) : new Collection();
        }
    }
} 