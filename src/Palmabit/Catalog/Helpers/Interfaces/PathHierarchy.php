<?php  namespace Palmabit\Catalog\Helpers\Interfaces; 
/**
 * Interface PathHierarchy
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface PathHierarchy 
{
    /**
     * Obtain the hierarchy path as array
     * @param $object
     * @return mixed
     */
    public function getHierarchyPathArray($object);
}