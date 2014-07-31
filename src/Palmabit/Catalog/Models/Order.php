<?php namespace Palmabit\Catalog\Models;
/**
 * Class Order
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use App, L;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Palmabit\Authentication\Exceptions\LoginRequiredException;
use Palmabit\Catalog\Exceptions\ProductEmptyException;
use Palmabit\Catalog\Presenters\OrderPresenter;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Exceptions\ValidationException;

class Order extends Model
{
    protected $table = 'order';

    protected $dates = ["date"];

    protected $fillable = ["completed","user_id","date"];

    protected $errors;

    /**
     * A collection of row_order
     * @var \Illuminate\Support\Collection
     */
    protected $row_orders;

    public function __construct()
    {
        $this->row_orders = new Collection();
        $this->errors = new MessageBag();
        return parent::__construct(func_get_args());
    }

    function __sleep()
    {
        $serialize_fields = array();

        $serialize_fields[] = 'row_orders';

        return $serialize_fields;
    }

    public function markCompleted()
    {
        $this->setAttribute('completed', true);
    }

    public function row_orders()
    {
        return $this->hasMany('Palmabit\Catalog\Models\RowOrder', 'order_id', 'id');
    }

    /**
     * Adds a row to his collection
     * @param Product $product
     * @param         $quantity
     * @param Palmabit\Catalog\Models\RowOder
     */
    public function addRow(Product $product, $quantity, RowOrder $row_order = null)
    {
        $this->checkForProductAvailability($product);

        $row = $row_order ? $row_order : new RowOrder();

        $quantity = $this->clearDuplicatesAndUpdateQuantity($product, $quantity);

        $row->setItem($product, $quantity);

        $this->row_orders->push($row);
    }

    public function clearDuplicatesAndUpdateQuantity($product, $quantity)
    {
        foreach ($this->row_orders as $key => $row_order) if($row_order->slug == $product->slug)
        {
            $this->row_orders->forget($key);
            $quantity+= $row_order->quantity;
        }

        return $quantity;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowOrders()
    {
        return $this->row_orders;
    }

    public function save(array $options = array())
    {
        if(! $this->validate()) throw new ValidationException;

        $this->markCompleted();
        $this->setupUserId();
        $this->setupDate();
        parent::save($options);

        $this->saveRows();

        return $this;
    }

    public function validate()
    {
        if($this->row_orders->isEmpty())
        {
            $this->errors->add("row_orders", L::t("There is no product in the cart.") );
            return false;
        }

        return true;
    }

    /**
     * Deletes a row given a product id
     * @param $product_id
     * @throws Palmabit\Library\Exceptions\NotFoundException
     */
    public function deleteRowOrder($product_id)
    {
        $success = false;
        foreach($this->row_orders as $key => $order)
        {
            if($order->product_id == $product_id)
            {
                $this->row_orders->forget($key);
                $success = true;
            }
        }
        if(! $success) throw new NotFoundException;

        return $this;
    }

    /**
     * changes a row quantity
     * @param $product_id
     * @param $quantity
     * @throws Palmabit\Library\Exceptions\NotFoundException
     */
    public function changeRowQuantity($product_id, $quantity)
    {
        $success = false;
        foreach($this->row_orders as $order_row)
        {
            if($order_row->product_id == $product_id)
            {
                $order_row->quantity = $quantity;
                try
                {
                    $order_row->total_price = $order_row->calculatePrice(Product::findOrFail($product_id), $quantity);
                }
                catch(ModelNotFoundException $e)
                {
                    throw new NotFoundException;
                }
                $success = true;
            }
        }
        if(! $success) throw new NotFoundException;

        return $this;
    }

    protected function setupUserId()
    {
        $logged_user = App::make('authenticator')->getLoggedUser();
        if (!$logged_user) throw new LoginRequiredException;
        $this->setAttribute('user_id', $logged_user->id);
    }

    protected function saveRows()
    {
        $this->row_orders->each(function ($order){
            $this->row_orders()->save($order);
        });
    }

    protected function setupDate()
    {
        $this->setAttribute('date', Carbon::now());
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function calculateTotalAmount()
    {
        $total_amount = 0;

        foreach ($this->row_orders()->get() as $row_order) {
            $total_amount+= $row_order->total_price;
        }

        return $total_amount;
    }

    public function getPresenter()
    {
        return new OrderPresenter($this);
    }

    /**
     * @param Product $product
     * @throws \Palmabit\Catalog\Exceptions\ProductEmptyException
     */
    protected function checkForProductAvailability(Product $product)
    {
        if(!$product->isAvailabile()) throw new ProductEmptyException;
    }

}