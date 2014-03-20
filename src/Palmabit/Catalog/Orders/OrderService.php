<?php  namespace Palmabit\Catalog\Orders; 
/**
 * Class OrderService
 *
 * @author jacopo beschi j.beschi@palamabit.com
 */
use Illuminate\Support\MessageBag;
use Palmabit\Authentication\Exceptions\LoginRequiredException;
use Palmabit\Library\Exceptions\InvalidException;
use Palmabit\Library\Exceptions\ValidationException;
use Session, Event, App, L, Log;
use Palmabit\Catalog\Models\Order;
use Palmabit\Catalog\Models\Product;
use Palmabit\Library\Email\MailerInterface;
use Palmabit\Library\Exceptions\PalmabitExceptionsInterface;
use Swift_TransportException;

class OrderService
{
    /**
     * The order
     * @var \Palmabit\Catalog\Models\Order
     */
    protected $order;

    /**
     *
     * @var Illuminate\Support\MessageBag
     */
    protected $errors;

    protected $session_key = "catalog_order";

    public function __construct()
    {
        $this->order = $this->getOrderInstance();
        $this->errors = new MessageBag();
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
        $this->order->getConnection()->getPdo()->beginTransaction();

        try
        {
            $this->order->save();
            $this->sendEmailToClient();
            $this->sendEmailToAdmin();
            $this->clearSession();
        }
        catch( LoginRequiredException $e)
        {
            $this->logMailErrors();
        }
        catch( Swift_TransportException $e)
        {
            $this->logMailErrors();
        }
        catch( ValidationException $e)
        {
            $this->order->getConnection()->getPdo()->rollback();
            $this->errors = $this->order->getErrors();
            throw new InvalidException;
        }

        $this->order->getConnection()->getPdo()->commit();

        return $this;
    }

    protected function clearSession()
    {
        Session::forget($this->session_key);
    }

    public function sendEmailToClient()
    {
        $mailer = App::make('palmamailer');
        // get the client email
        $email = $this->getClientEmail();
        // send the email with the information
        $mailer->sendTo($email, ["order" => $this->order, 'email' => $email] , L::t('Order number:').$this->order->id.' '.L::t('created succesfully'), 'catalog::mail.order-sent-client');
    }

    public function sendEmailToAdmin()
    {
        $mailer = App::make('palmamailer');
        // get the admin emails
        $mail_helper = App::make('authentication_helper');
        $mails       = $mail_helper->getNotificationRegistrationUsersEmail();
        if (!empty($mails)) foreach ($mails as $email)
        {
            $mailer->sendTo($email, ["order" => $this->order, 'email' => $email] , 'Ordine: '.$this->order->id.' creato', 'catalog::mail.order-sent-admin');
        }
    }

    public function deleteRow($product_id)
    {
        return $this->order->deleteRowOrder($product_id);
    }

    public function changeRowQuantity($product_id, $quantity)
    {
        return $this->order->changeRowQuantity($product_id, $quantity);
    }

    protected function getClientEmail()
    {
        $user = App::make('authenticator')->getLoggedUser();

        if (!$user) throw new LoginRequiredException;

        return $user->email;
    }

    /**
     * @return \Palmabit\Catalog\Orders\Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function logMailErrors()
    {
        $this->order->getConnection()->getPdo()->rollback();
        $this->errors->add("email", "There was an error sending the email.");
        Log::error('error sending email.');
        throw new InvalidException;
    }

}