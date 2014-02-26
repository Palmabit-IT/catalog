<?php

Route::group( ['before' => ['logged'] ], function()
{
    /////////////////////////////////////// CATEGORY /////////////////////////////////////
    Route::get('/admin/category/lists',['as'=>'category.lists','uses' =>'Palmabit\Catalog\Controllers\CategoryController@lists']);
    Route::get('/admin/category/edit',['as'=>'category.modifica','uses' =>'Palmabit\Catalog\Controllers\CategoryController@getEdit']);
    Route::post('/admin/category/edit',['as'=>'category.modifica', "before"=>"csrf", 'uses' =>'Palmabit\Catalog\Controllers\CategoryController@postEdit']);
    //@todo add csrf filter ui
    Route::any('/admin/category/delete',['as'=>'category.cancella','uses' =>'Palmabit\Catalog\Controllers\CategoryController@delete']);

    ////////////////////////////////////// PRODUCT //////////////////////////////////
    Route::get('/admin/products/lists',['as'=>'products.lists','uses' =>'Palmabit\Catalog\Controllers\ProductsController@lists']);
    Route::get('/admin/products/edit',['as'=>'products.edit','uses' =>'Palmabit\Catalog\Controllers\ProductsController@getEdit']);
    Route::post('/admin/products/edit',['as'=>'products.edit', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postEdit']);
    //@todo add csrf ui
    Route::any('/admin/products/delete',['as'=>'products.delete','uses' =>'Palmabit\Catalog\Controllers\ProductsController@delete']);
    //////////////// PRODUCT CATEGORIES //////////////////
    Route::post('/admin/products/category/edit',['as'=>'products.category', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postCategory']);
    ////////////////////// PRODUCT IMAGE ////////////////////////
    Route::post('/admin/products/images/edit',['as'=>'products.images', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postImage']);
    //@todo add csrf ui
    Route::any('/admin/products/images/delete',['as'=>'products.images.delete','uses' => 'Palmabit\Catalog\Controllers\ProductsController@deleteImage']);
    //@todo add csrf ui
    Route::any('/admin/products/images/featured/{id}/{product_id}',['as'=>'products.immagini.evidenza','uses' =>'Palmabit\Catalog\Controllers\ProductsController@postFeatured']);
    Route::post('/admin/products/order', ["before" => "csrf", 'uses' => 'Palmabit\Catalog\Controllers\ProductsController@postChangeOrder']);
});