<?php  namespace Palmabit\Catalog\Models;
/**
 * Class RowOrder
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Cartalyst\Sentry\Users\LoginRequiredException;
use Illuminate\Database\Eloquent\Model;
use App, Config;
class RowOrder extends Model
{
    protected $table = "row_order";

    protected $fillable = ["order_id", "product_id", "quantity", "total_price"];

    protected $product_id;
    protected $quantity;
    protected $total_price;

    public function order()
    {
        return $this->belongsTo('Palmabit\Catalog\Models\Order','order_id');
    }

    public function product()
    {
        return $this->belongsTo('Palmabit\Catalog\Models\Product','product_id');
    }
    
    /**
     * @param Product $product
     * @param Integer $quantity
     */
    public function setItem(Product $product, $quantity)
    {

    }

    /**
     * Calculates the total price
     * @param Product $product
     * @param Integer $quantity
     */
    public function calculatePrice(Product $product, $quantity)
    {
        $group_professional = Config::get('catalog::groups.professional_group_name');
        $group_logged = Config::get('catalog::groups.logged_group_name');
        $authenticator = App::make('authenticator');

        if(! $authenticator->check()) throw new LoginRequiredException;

        if($authenticator->hasGroup($group_professional)) return $product->price2;
        if($authenticator->hasGroup($group_logged)) return $product->price1;
    }
} 