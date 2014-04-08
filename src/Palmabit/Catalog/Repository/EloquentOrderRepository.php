<?php  namespace Palmabit\Catalog\Repository;
use Palmabit\Catalog\Models\Order;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Config;

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

    /**
     * @override
     * @return mixed
     * @todo tests
     */
    public function all()
    {
        $per_page = Config::get('catalog::orders_per_page',5);
        return $this->model->paginate($per_page);
    }

    public function calculateTotalAmount($order_id)
    {
        $order = $this->find($order_id);

        return $order->calculateTotalAmount();
    }

    public function getOrderByUserId($user_id)
    {
        return $this->model->where('user_id', '=', $user_id)
                    ->get();
    }
}
