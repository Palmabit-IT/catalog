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
    //////////////// PRODUCT ACCESSORIES //////////////////
    Route::post('/admin/products/products/attach',['as'=>'products.accessories.attach', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postAttachProduct']);
    Route::any('/admin/products/products/detach',['as'=>'products.accessories.detach', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postDetachProduct']);
    //////////////// PRODUCT CATEGORIES //////////////////
    Route::get('/admin/products/category/detach',['as'=>'products.category.detach', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postDetachCategory']);
    Route::post('/admin/products/category/attach',['as'=>'products.category.attach', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postAttachCategory']);
    ////////////////////// PRODUCT IMAGE ////////////////////////
    Route::post('/admin/products/images/edit',['as'=>'products.images', "before" => "csrf", 'uses' =>'Palmabit\Catalog\Controllers\ProductsController@postImage']);
    //@todo add csrf ui
    Route::any('/admin/products/images/delete',['as'=>'products.images.delete','uses' => 'Palmabit\Catalog\Controllers\ProductsController@deleteImage']);
    //@todo add csrf ui
    Route::any('/admin/products/images/featured/{id}/{product_id}',['as'=>'products.immagini.evidenza','uses' =>'Palmabit\Catalog\Controllers\ProductsController@postFeatured']);
    Route::post('/admin/products/order', ["before" => "csrf", 'uses' => 'Palmabit\Catalog\Controllers\ProductsController@postChangeOrder']);
});