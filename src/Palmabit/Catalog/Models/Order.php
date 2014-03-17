<?php  namespace Palmabit\Catalog\Models;
/**
 * Class Order
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Order extends Model
{
    /**
     * The status of the order
     * @var String
     */
    protected $status;

    protected $order_rows;

    public function __construct()
    {
        $this->order_rows = new Collection();
        return parent::__construct(func_get_args());
    }

    protected $dates = ["date"];

    protected $fillable = ["status","user_id","date"];

    public function markCompleted()
    {

    }

}