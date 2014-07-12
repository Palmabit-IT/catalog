<?php
Route::group(['before' => ['logged']], function ()
{
    /////////////////////////////////////// CATEGORY /////////////////////////////////////
    Route::get('/admin/category/lists', [
            'as'   => 'category.lists',
            'uses' => 'Palmabit\Catalog\Controllers\CategoryController@lists'
    ]);
    Route::get('/admin/category/edit', [
            'as'   => 'category.modifica',
            'uses' => 'Palmabit\Catalog\Controllers\CategoryController@getEdit'
    ]);
    Route::post('/admin/category/edit', [
            'as'     => 'category.modifica',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\CategoryController@postEdit'
    ]);

    //@todo add csrf filter ui!!! SECURITY BUG
    Route::any('/admin/category/delete', [
            'as'   => 'category.cancella',
            'uses' => 'Palmabit\Catalog\Controllers\CategoryController@delete'
    ]);
    Route::post('/admin/category/setparent', [
            'as'   => 'category.setparent',
            'uses' => 'Palmabit\Catalog\Controllers\CategoryController@postSetParent'
    ]);
    Route::post('/admin/category/setparentlists', [
            'as'   => 'category.setparentfromlist',
            'uses' => 'Palmabit\Catalog\Controllers\CategoryController@postSetParentList'
    ]);
    Route::post('/admin/category/changeimage', [
            'as'   => 'category.changeimage',
            'uses' => 'Palmabit\Catalog\Controllers\CategoryController@postUpdateImage'
    ]);
    Route::post('/admin/category/order', [
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\CategoryController@postChangeOrder'
    ]);

    ////////////////////////////////////// PRODUCT //////////////////////////////////
    Route::get('/admin/products/lists', [
            'as'   => 'products.lists',
            'before' => 'force_default_admin_language',
            'uses' => 'Palmabit\Catalog\Controllers\ProductsController@lists'
    ]);
    Route::get('/admin/products/edit', [
            'as'   => 'products.edit',
            'uses' => 'Palmabit\Catalog\Controllers\ProductsController@getEdit'
    ]);
    Route::post('/admin/products/edit', [
            'as'     => 'products.edit',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postEdit'
    ]);
    Route::any('/admin/product/duplicate', [
            'before' => 'csrf',
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@duplicate'
    ]);
    //@todo add csrf filter ui!!! SECURITY BUG
    Route::any('/admin/products/delete', [
            'as'   => 'products.delete',
            'uses' => 'Palmabit\Catalog\Controllers\ProductsController@delete'
    ]);
    Route::any('/admin/products/deletebysluglang', [
            'as'   => 'products.delete.bysluglang',
            'uses' => 'Palmabit\Catalog\Controllers\ProductsController@deleteBySlugLang'
    ]);

    //////////////// PRODUCT ACCESSORIES //////////////////
    Route::post('/admin/products/products/attach', [
            'as'     => 'products.accessories.attach',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postAttachProduct'
    ]);
    Route::any('/admin/products/products/detach', [
            'as'     => 'products.accessories.detach',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postDetachProduct'
    ]);

    //////////////// PRODUCT CATEGORIES //////////////////
    Route::get('/admin/products/category/detach', [
            'as'     => 'products.category.detach',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postDetachCategory'
    ]);
    Route::post('/admin/products/category/attach', [
            'as'     => 'products.category.attach',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postAttachCategory'
    ]);

    ////////////////////// PRODUCT IMAGE ////////////////////////
    Route::post('/admin/products/images/edit', [
            'as'     => 'products.images',
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postImage'
    ]);
    //@todo add csrf ui!!! SECURITY BUG
    Route::any('/admin/products/images/delete', [
            'as'   => 'products.images.delete',
            'uses' => 'Palmabit\Catalog\Controllers\ProductsController@deleteImage'
    ]);
    //@todo add csrf ui!!! SECURITY BUG
    Route::any('/admin/products/images/featured/{id}/{product_id}', [
            'as'   => 'products.immagini.evidenza',
            'uses' => 'Palmabit\Catalog\Controllers\ProductsController@postFeatured'
    ]);

    ////////////////////// PRODUCT ORDER ////////////////////////
    Route::post('/admin/products/order', [
            "before" => "csrf",
            'uses'   => 'Palmabit\Catalog\Controllers\ProductsController@postChangeOrder'
    ]);
    Route::get('/admin/orders/lists', [
            "as"   => "products.order.lists",
            'uses' => 'Palmabit\Catalog\Controllers\OrderController@lists'
    ]);
    Route::get('/admin/orders/show/{id}', [
            "as"   => "products.order.show",
            'uses' => 'Palmabit\Catalog\Controllers\OrderController@show'
    ]);
});