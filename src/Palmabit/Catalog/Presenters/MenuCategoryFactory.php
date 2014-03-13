<?php  namespace Palmabit\Catalog\Presenters;
use Config, App;
use Illuminate\Support\Collection;
use Palmabit\Catalog\Menu\MenuItem;
use Palmabit\Catalog\Models\Category;

/**
 * Class MenuCategoryFactory
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class MenuCategoryFactory
{
    protected $cat_type = "category";
    protected $product_type = "product";

    public function __construct()
    {
        $this->r = App::make('category_repository');
    }

    /**
     * Create the category menu with his childrens
     * @todo test
     */
    public function create(Category $category)
    {
        // get the categories
        $columns = ["id", "description", "slug_lang"];
        $categories = $this->r->getSiblingsAndSelf($category->id, $columns);
        $cat_menu = new Collection();
        if($categories) foreach ($categories as $category)
        {
            // get the childrens
            $childrens = ( ! $category->children()->get()->isEmpty()) ? $category->children()->get(["id", "description", "slug_lang"]) : $category->products()->get(["product.id", "name", "slug_lang"]);
            // create the menuitems
            $cat_menu_item = new MenuItem($category->description, $category->slug_lang, $this->cat_type);
            if($childrens) foreach ($childrens as $children)
            {
                if(is_a($children, 'Palmabit\Catalog\Models\Category'))
                    $cat_menu_item->add(new MenuItem($children->description, $children->slug_lang, $this->cat_type));
                else
                {
                    // do nothing
                }
            }
            // append to original menu
            $cat_menu->push($cat_menu_item);
        }

        return $cat_menu;
    }
} 