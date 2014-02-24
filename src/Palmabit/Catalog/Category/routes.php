<?php

Route::group( ['before' => ['logged'] ], function()
{
    /////////////////////////////////////// GENERALE /////////////////////////////////////
    Route::get('/admin/category',['as'=>'categorie.list','uses' =>'Category\Controllers\CategoryController@lista']);
    Route::get('/admin/category/modifica',['as'=>'categorie.modifica','uses' =>'Category\Controllers\CategoryController@getModifica']);
    Route::post('/admin/category/modifica',['as'=>'categorie.modifica', "before"=>"csrf", 'uses' =>'Category\Controllers\CategoryController@postModifica']);
    //@todo add csrf filter ui
    Route::any('/admin/category/cancella',['as'=>'categorie.cancella','uses' =>'Category\Controllers\CategoryController@cancella']);
    /////////////////////////////////////// ACCESSORI /////////////////////////////////////
    Route::post('/admin/category/accessori/associa',['as'=>'categorie.cancella', "before" => "csrf", 'uses' =>'Category\Controllers\CategoryController@associaAccessorio']);
    //@todo add csrf filter ui
    Route::any('/admin/category/accessori/deassocia',['as'=>'categorie.cancella', 'uses' =>'Category\Controllers\CategoryController@deassociaAccessorio']);
});