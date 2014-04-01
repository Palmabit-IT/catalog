<?php  namespace Palmabit\Catalog\Repository;
use Palmabit\Catalog\Models\Order;
use Palmabit\Library\Repository\EloquentBaseRepository;

/**
 * Class EloquentOrderRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class EloquentOrderRepository extends EloquentBaseRepository
{
    public function __construct()
    {
        return parent::__construct(new Order);
    }
} 