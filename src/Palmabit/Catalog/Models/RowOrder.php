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

    protected $fillable = ["order_id", "product_id", "quantity", "total_price", "slug", "price_type_used", "single_price"];

    protected $authenticator;

    protected $group_professional;

    protected $group_logged;

    public function getAuthenticator()
    {
        return isset($this->authenticator) ? $this->authenticator : App::make('authenticator');
    }

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
        $this->setAttribute('slug', $product->slug);
        $this->setAttribute('quantity', $quantity);
        $this->setAttribute('total_price', $this->calculatePrice($product, $quantity));
        $this->setAttribute('single_price', $this->getSingleProductPriceToUse($product, $quantity));
        $this->setAttribute('price_type_used', $this->getPriceTypeStringToUse($product, $quantity));
    }

    /**
     * @param Product $product
     * @param Integer $quantity
     */
    public function calculatePrice(Product $product, $quantity)
    {
        $this->group_professional = Config::get('catalog::groups.professional_group_name');
        $this->group_logged = Config::get('catalog::groups.logged_group_name');

        if(! $this->getAuthenticator()->check()) throw new LoginRequiredException;

        return $this->multiplyMoney($this->getSingleProductPriceToUse($product, $quantity
        ), $quantity);
    }

    public function getSingleProductPriceToUse($product, $quantity)
    {
        $price_type = $this->getPriceTypeStringToUse($product, $quantity);

        return $product->$price_type;
    }

    public function getPriceTypeStringToUse($product, $quantity)
    {
        if($this->getAuthenticator()->hasGroup($this->group_professional)
        &&
        $this->productQuantityIsMoreThenQuantityStep($product, $quantity))
            return "price4";
        elseif($this->getAuthenticator()->hasGroup($this->group_professional)
                &&
               ! $this->productQuantityIsMoreThenQuantityStep($product, $quantity))
            return "price3";
        elseif($this->getAuthenticator()->hasGroup($this->group_logged)
                &&
           $this->productQuantityIsMoreThenQuantityStepNonProfessional($product, $quantity))
            return "price2";
        else
            return "price1";

    }

    protected function multiplyMoney($price, $quantity)
    {
        return round( ($price * $quantity) , 2);
    }

    public function getProductPresenter()
    {
        return new PresenterProducts($this->product);
    }

    /**
     * @param $product
     * @param $quantity
     * @return bool
     */
    private function productQuantityIsMoreThenQuantityStep($product, $quantity)
    {
        return $product->quantity_pricing_enabled && $quantity >= $product->quantity_pricing_quantity;
    }

    private function productQuantityIsMoreThenQuantityStepNonProfessional($product, $quantity)
    {
        return $product->quantity_pricing_enabled && $quantity >= $product->quantity_pricing_quantity_non_professional;
    }

} 