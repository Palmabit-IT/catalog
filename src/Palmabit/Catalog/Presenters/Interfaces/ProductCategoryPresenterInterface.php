<?php  namespace Palmabit\Catalog\Presenters\Interfaces; 
/**
 * Interface ProductCategoryPresenterInterface
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
interface ProductCategoryPresenterInterface 
{
    public function featured_image();
    public function name();
    public function getLink();
}