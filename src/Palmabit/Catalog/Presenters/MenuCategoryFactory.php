<?php  namespace Palmabit\Catalog\Presenters;
use Config, App, L;
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

    protected $slug;

    public function __construct($slug = null)
    {
        $this->slug = $slug;
        $this->r = App::make('category_repository');
    }

    /**
     * Create the category menu with his childrens
     * @todo test
     */
    public function create()
    {
        // get the categories
        $categories = $this->r->getRootNodes();
        $cat_menu = new Collection();
        if($categories) foreach ($categories as $category)
        {
            // get the childrens
            $childrens = ( $category->children()->whereLang(L::get())->count() ) ? $category->children()->whereLang(L::get())->get(["id", "description", "slug_lang"]) : null;
            // create the menuitems
            //@todo handle multiple recursive subitems with a better algorithm
            $cat_menu_item = new MenuItem($category->description, $category->slug_lang, $this->cat_type, null, $this->getActive($category->slug_lang));
            if($childrens) foreach ($childrens as $children)
            {
                $children_item = new MenuItem($children->description, $children->slug_lang, $this->cat_type, null, $this->getActive($children->slug_lang));
                // if has sub-subcategories
                if($children->children()->whereLang(L::get())->count())
                {
                    $childrens_children = $children->children()->whereLang(L::get())->get(["id", "description", "slug_lang"]);
                    foreach ($childrens_children as $children_children)
                    {
                        $children_item->add(new MenuItem($children_children->description, $children_children->slug_lang, $this->cat_type, null, $this->getActive($children_children->slug_lang)) );
                    }
                }

                $cat_menu_item->add($children_item);
            }

            // append to original menu
            $cat_menu->push($cat_menu_item);
        }

        return $cat_menu;
    }

    protected function getActive($menu_slug)
    {
        return ($this->slug == $menu_slug) ? true : false;
    }
} 