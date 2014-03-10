<?php  namespace Palmabit\Catalog\Interfaces;
/**
 * Class TreeInterface
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
interface TreeInterface
{
    public function getParent($id);

    public function hasChildrens($id);

    public function getChildrens($id);

    public function setParent($id, $parent_id);

    public function setRoot($id);

    public function getRootNodes();

    public function getSiblians($id);

    public function getSiblingsAndSelf($id, Array $columns);
}