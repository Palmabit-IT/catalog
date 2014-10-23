<?php
use Palmabit\Catalog\Orders\OrderService;

    /**
 * Passa le voci di menu al pannello admin categoria
 */
View::composer('catalog::category.*', function($view){
    $view->with('sidebar_items', [
                                "Lista categorie" => [URL::to('/admin/category/lists'),"<i class='glyphicon glyphicon-th-list'></i>"],
                                "Aggiungi categoria" => [URL::to('/admin/category/edit'), "<i class='glyphicon glyphicon-plus'></i>"],
                           ]);
});

/**
 * Passa le voci di menu al pannello admin prodotti
 */
View::composer(['catalog::products.*', 'catalog::orders.*'], function($view){
    $view->with('sidebar_items', [
                                "Lista prodotti" => [URL::to('/admin/products/lists'),"<i class='glyphicon glyphicon-th-list'></i>"],
                                "Aggiungi prodotti" => [ URL::to('/admin/products/edit'),"<i class='glyphicon glyphicon-plus'></i>"],
                                "Lista Ordini" => [URL::to('/admin/orders/lists'),"<i class='glyphicon glyphicon-credit-card'></i>"]
                           ]);
});
// elementi presenti nel carrello
View::composer(['*'], function($view){
    $service = new OrderService();

    $order = $service->getOrder();
    $row_order = $order->getRowOrders();

    $view->with('row_orders', $row_order);
});

/**
 * Send to the view the logged user
 */
View::composer('*', function ($view){
    $logged_user = App::make('authenticator')->getLoggedUser();

    $view->with('logged_user', $logged_user );
});