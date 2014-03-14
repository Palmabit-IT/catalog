<?php  namespace Palmabit\Catalog\Menu;
use Illuminate\Support\Collection;

/**
 * Class MenuItem
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class MenuItem
{
    /**
     * @var String
     */
    protected $name;
    /**
     * @var String
     */
    protected $slug;
    /**
     * Icon class if given
     * @var String
     */
    protected $icon;
    /**
     * Menu item type
     * @var String
     */
    protected $type;
    /**
     * Collection of menuitems
     * @var \Illuminate\Support\Collection
     */
    protected $subitems_collection;
    /**
     * If the current menu item is active
     * @var Boolean
     */
    protected $active;
    /**
     * @var Boolean
     */
    protected $open;

    function __construct($name, $slug, $type, Collection $collection = null, $active = false, $icon = null)
    {
        $this->icon = $icon;
        $this->type = $type;
        $this->name = $name;
        $this->slug = $slug;
        $this->active = $active;
        $this->subitems_collection = $collection ? $collection : new Collection();
    }

    /**
     * @param boolean $open
     */
    public function setOpen($open)
    {
        $this->open = $open;
    }

    /**
     * @return boolean
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function hasItems()
    {
        return ! $this->subitems_collection->isEmpty();
    }

    public function getCollection()
    {
        return $this->subitems_collection;
    }

    public function add($item, $key = null)
    {
        if($key)
            $this->subitems_collection->put($key, $item);
        else
            $this->subitems_collection->push($item);
    }
}
