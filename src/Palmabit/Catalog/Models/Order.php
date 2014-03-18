<?php namespace Palmabit\Catalog\Models;
/**
 * Class Order
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App;
use Carbon\Carbon;
use Palmabit\Authentication\Exceptions\LoginRequiredException;

class Order extends Model
{
    protected $table = 'order';

    protected $dates = ["date"];

    protected $fillable = ["completed","user_id","date"];

    /**
     * A collection of row_order
     * @var \Illuminate\Support\Collection
     */
    protected $row_orders;

    public function __construct()
    {
        $this->row_orders = new Collection();
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
        $row = $row_order ? $row_order : new RowOrder();
        $row->setItem($product, $quantity);

        $this->row_orders->push($row);
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
        $this->markCompleted();
        $this->setupUserId();
        $this->setupDate();
        parent::save(func_get_args());

        $this->saveRows();

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

}