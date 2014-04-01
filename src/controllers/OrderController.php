<?php  namespace Palmabit\Catalog\Controllers;
use App, Controller, View;
use Palmabit\Catalog\Presenters\OrderPresenter;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Presenters\PresenterCollection;

/**
 * Class OrderController
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class OrderController extends Controller
{

    /**
     * @var
     */
    protected $r;

    public function __construct()
    {
        $this->r = App::make('order_repository');
    }

    public function lists()
    {
        $users = $this->r->all();
        $presenter = new PresenterCollection('Palmabit\Catalog\Presenters\OrderPresenter', $users);
        return View::make('catalog::orders.show')->with('orders', $presenter);
    }

    public function show($id)
    {
        try
        {
            $order = $this->r->find($id);
            $presenter = new OrderPresenter($order);
        }
        catch(NotFoundException $e)
        {
            //@todo
        }

        return View::make('catalog::orders.detail')->with(["order_presenter" => $presenter, 'order' => $order]);
    }
} 