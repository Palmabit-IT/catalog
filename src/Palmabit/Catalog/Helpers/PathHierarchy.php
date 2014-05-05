<?php  namespace Palmabit\Catalog\Helpers; 
/**
 * Class PathHierarchy
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\Helpers\Interfaces\PathHierarchy as PathHierarchyInterface;
use App;

class PathHierarchy implements PathHierarchyInterface
{
    protected $hierarchy_nodes = [];
    protected $product_class;
    protected $category_class;

    public function __construct()
    {
        $this->product_class = 'Palmabit\Catalog\Models\Product';
        $this->category_class = 'Palmabit\Catalog\Models\Category';
    }

    /**
     * Obtain the hierarchy path as array
     * @param $object
     * @return mixed
     */
    public function getHierarchyPathArray($object)
    {
        $child = $object;
        array_push($this->hierarchy_nodes, $child);

        while( $parent = $this->getParentNode($child) )
        {
            array_push($this->hierarchy_nodes, $parent);
            $child = $parent;
        }

        return array_reverse($this->hierarchy_nodes);
    }

    protected function getParentNode($child)
    {

        if(is_a($child, $this->product_class))
        {
            if($child->categories->isEmpty()) return false;
            // get first category
            return $child->categories->first();
        }
        elseif(is_a($child, $this->category_class))
        {
            // get category parent
            return App::make('category_repository')->getParent($child->id)->first();
        }

        return false;
    }
}