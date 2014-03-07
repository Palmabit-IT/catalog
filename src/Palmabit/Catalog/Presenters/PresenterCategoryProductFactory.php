<?php  namespace Palmabit\Catalog\Presenters;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Palmabit\Catalog\Models\Category;
use Palmabit\Library\Presenters\PresenterPagination;

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
     * @todo test
     */
    public function create(Category $category)
    {
        $per_page = 10;
        // if has childrens returns the subcategories
        if($this->r->hasChildrens($category->id))
        {
            $categories = ($category->children) ? $category->children()->paginate($per_page) : array();
            return ( ! empty($categories) ) ? new PresenterPagination('Palmabit\Catalog\Presenters\PresenterCategory', $categories ) : new Collection();
        }
        else
        {
            $products = isset($category->products) ? $category->products()->paginate($per_page) : array();
            return ( ! empty($products) ) ? new PresenterPagination('Palmabit\Catalog\Presenters\PresenterProducts', $products ) : new Collection();
        }
    }
} 