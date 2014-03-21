<?php  namespace Palmabit\Catalog\Models;
/**
 * Class RowOrder
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Illuminate\Database\Eloquent\Model;
use Palmabit\Authentication\Exceptions\LoginRequiredException;
use App, Config;
use Palmabit\Catalog\Presenters\PresenterProducts;

class RowOrder extends Model
{
    protected $table = "row_order";

    protected $fillable = ["order_id", "product_id", "quantity", "total_price"];

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
        $this->setAttribute('product_id', $product->id);
        $this->setAttribute('quantity', $quantity);
        $this->setAttribute('total_price', $this->calculatePrice($product, $quantity));
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

        if($product->quantity_pricing_enabled && $quantity >= $product->quantity_pricing_quantity)
        {
            if($authenticator->hasGroup($group_professional)) return $this->multiplyMoney($product->price3,$quantity);
            if($authenticator->hasGroup($group_logged)) return $this->multiplyMoney($product->price2,$quantity);
        }

        if($authenticator->hasGroup($group_professional)) return $this->multiplyMoney($product->price2,$quantity);
        if($authenticator->hasGroup($group_logged)) return $this->multiplyMoney($product->price1,$quantity);
    }

    protected function multiplyMoney($price, $quantity)
    {
        return round( ($price * $quantity) , 2);
    }

    public function getProductPresenter()
    {
        return new PresenterProducts($this->product);
    }

} 