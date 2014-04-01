<?php  namespace Palmabit\Catalog\Presenters;
use Palmabit\Library\Presenters\AbstractPresenter;
use App;
use Carbon\Carbon;
/**
 * Class OrderPresenter
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class OrderPresenter extends AbstractPresenter
{

    /**
     * Authenticator implementation
     * @var
     */
    protected $authenticator;

    public function __construct($resource)
    {
        $this->authenticator = App::make('authenticator');
        return parent::__construct($resource);
    }

    public function total_price()
    {
        $total = 0;
        $rows =  $this->resource->row_orders()->get();
        foreach ($rows as $row)
        {
            $total+= $row->total_price;
        }

        return $total;
    }

    public function date()
    {
        $european_date = 'd/m/y';

        return $this->resource->date->format($european_date);
    }

    public function author_email()
    {
        $user = $this->authenticator->findById($this->resource->user_id);

        return $user->email;
    }

}