<?php

use Palmabit\Catalog\Orders\OrderService;

Event::listen('authentication.login', function($user){
    $order_service = new OrderService();
    $order_service->clearSession();
});

