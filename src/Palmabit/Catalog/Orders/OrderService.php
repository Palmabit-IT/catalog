<?php  namespace Palmabit\Catalog\Orders; 
/**
 * Class OrderService
 *
 * @author jacopo beschi j.beschi@palamabit.com
 */
use Palmabit\Authentication\Exceptions\LoginRequiredException;
use Session, Event, App, L;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Models\Product;
use Palmabit\Library\Email\MailerInterface;

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
        Event::listen('order.created', 'Palmabit\Catalog\Orders\OrderService@sendEmailToClient');
        Event::listen('order.created', 'Palmabit\Catalog\Orders\OrderService@sendEmailToAdmin');
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
            Event::fire('order.created', $this->order);
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

    public function sendEmailToClient(MailerInterface $mailer)
    {
        // get the client email
        $email = $this->getClientEmail();
        // send the email with the information
        $mailer->sendTo($email, ["order" => $this->order, 'email' => $email] , L::t('Order number:').$this->order->id.' '.L::t('created succesfully'), 'catalog:mail.order-sent-client');
    }

    public function sendEmailToAdmin(MailerInterface $mailer)
    {
        // get the admin emails
        $mail_helper = App::make('authentication_helper');
        $mails       = $mail_helper->getNotificationRegistrationUsersEmail();
        if (!empty($mails)) foreach ($mails as $email)
        {
            $mailer->sendTo($email, ["order" => $this->order, 'email' => $email] , 'Ordine: '.$this->order->id.' creato', 'catalog:mail.order-sent-admin');
        }
    }

    protected function getClientEmail()
    {
        $user = App::make('authenticator')->getLoggedUser();

        if (!$user) throw new LoginRequiredException;

        return $user->email;
    }

}