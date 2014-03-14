<?php  namespace Palmabit\Catalog\Presenters;
use Palmabit\Library\Presenters\PresenterCollection;
use Illuminate\Support\Collection;
use App;
/**
 * Class PresenterAccessoriesFactory
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class PresenterAccessoriesFactory 
{
    public function __construct()
    {
        $this->r = App::make('product_repository');
    }

    /**
     * @param $product_id
     * @return PresenterCollection
     * @throws Palmabit\Library\Exceptions\NotFoundException
     */
    public function create($product_id)
    {
        $accessories = $this->r->getAccessories($product_id);
        // create the collection
        return new PresenterCollection('Palmabit\Catalog\Presenters\PresenterProducts', $accessories);
    }
}