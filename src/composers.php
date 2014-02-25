<?php

/**
 * Passa le voci di menu al pannello admin categoria
 */
View::composer('catalog::category.*', function($view){
    $view->with('sidebar', array(
                                "Lista categorie" => URL::to('/admin/category/lists'),
                                "Aggiungi categoria" => URL::to('/admin/category/edit'),
                           ));
});

/**
 * Passa le voci di menu al pannello admin prodotti
 */
View::composer(['catalog::products.*'], function($view){
    $view->with('sidebar', array(
                                "Lista prodotti" => URL::to('/admin/products/lists'),
                                "Aggiungi prodotti" => URL::to('/admin/products/edit')
                           ));
});