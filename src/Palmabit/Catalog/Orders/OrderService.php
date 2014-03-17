<?php  namespace Palmabit\Catalog\Orders; 
/**
 * Class OrderService
 *
 * @author jacopo beschi j.beschi@palamabit.com
 */
use Palmabit\Authentication\Exceptions\LoginRequiredException;
use Session, Event, App;
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
        Event::listen('order.creating', 'Palmabit\Catalog\Orders\OrderService@sendEmailToClient');
        Event::listen('order.creating', 'Palmabit\Catalog\Orders\OrderService@sendEmailToAdmin');
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
            Event::fire('order.creating', $this->order);
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

    public function sendEmailToClient()
    {
        //@todo
        // get the client email
        $this->getClientEmail();
        // send the email with the information
    }

    public function sendEmailToAdmin()
    {
        //@todo
        // get the admin email
        // send the email with the information
    }

    protected function getClientEmail()
    {
        $user = App::make('authenticator')->getLoggedUser();
        if (!$user) throw new LoginRequiredException;

        $email = $user->email;
    }

}