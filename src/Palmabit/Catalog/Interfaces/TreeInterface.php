<?php  namespace Palmabit\Catalog\Interfaces; 
/**
 * Interface TreeInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface TreeInterface 
{
    public function getParent($id);

    public function getChildrens($id);

    public function setParent($id, $parent_id);

    public function setRoot($id, $parent_id);

}