<?php  namespace Palmabit\Catalog\Orders; 
/**
 * Class OrderService
 *
 * @author jacopo beschi j.beschi@palamabit.com
 */
use Session;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Models\Product;

class OrderService 
{
    /**
     * The order
     * @var \Palmabit\Catalog\Models\Order
     */
    protected $order;

    protected $session_key = "catalog_order";

    public function __construct()
    {
        $this->order = $this->getOrderInstance();
    }

    public function getOrderInstance()
    {
        return Session::has($this->session_key) ? Session::get($this->session_key) : new Order;
    }

    /**
     * @return \Palmabit\Catalog\Models\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getSessionKey()
    {
        return $this->session_key;
    }

    /**
     * @param Product $product
     * @param         $quantity
     * @param null    $row_order
     */
    public function addRow(Product $product, $quantity, $row_order = null)
    {
        $this->order->addRow($product, $quantity, $row_order);
        // update the session
        Session::put($this->session_key, $this->order);
    }

    public function commit()
    {
        $this->order->getConnection('authentication')->getPdo()->beginTransaction();
        $success = $this->order->save();
        if($success)
        {
            $this->order->getConnection()->getPdo()->commit();
            $this->clearSession();
        }
        else
        {
            $this->order->getConnection()->getPdo()->rollback();
        }
    }

    protected function clearSession()
    {
        Session::forget($this->session_key);
    }

}