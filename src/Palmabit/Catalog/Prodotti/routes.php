<?php

Route::group( ['before' => ['logged'] ], function()
{
    Route::get('/admin/prodotti/list',['as'=>'prodotti.list','uses' =>'Prodotti\Controllers\ProdottoController@lista']);
    ////////////////////// PRODOTTO BASE //////////////////////
    Route::get('/admin/prodotti/modifica',['as'=>'prodotti.modifica','uses' =>'Prodotti\Controllers\ProdottoController@getModifica']);
    Route::post('/admin/prodotti/modifica',['as'=>'prodotti.modifica', "before" => "csrf", 'uses' =>'Prodotti\Controllers\ProdottoController@postModifica']);
    //@todo add csrf ui
    Route::any('/admin/prodotti/cancella',['as'=>'prodotti.cancella','uses' =>'Prodotti\Controllers\ProdottoController@cancella']);
    //////////////// CATEGORIE PRODOTTO //////////////////
    Route::post('/admin/prodotti/categoria/modifica',['as'=>'prodotti.categoria', "before" => "csrf", 'uses' =>'Prodotti\Controllers\ProdottoController@postCategoria']);
    ////////////////////// IMMAGINI PRODOTTO ////////////////////////
    Route::post('/admin/prodotti/immagini/modifica',['as'=>'prodotti.immagini', "before" => "csrf", 'uses' =>'Prodotti\Controllers\ProdottoController@postImmagine']);
    //@todo add csrf ui
    Route::any('/admin/prodotti/immagini/cancella',['as'=>'prodotti.immagini.cancella','uses' => 'Prodotti\Controllers\ProdottoController@cancellaImmagine']);
    //@todo add csrf ui
    Route::any('/admin/prodotti/immagini/evidenza/{id}/{prodotto_id}',['as'=>'prodotti.immagini.evidenza','uses' =>'Prodotti\Controllers\ProdottoController@postInEvidenza']);
    Route::post('/admin/prodotti/ordina', ["before" => "csrf", 'uses' => 'Prodotti\Controllers\ProdottoController@postModificaOrdine']);
    ///////////////////////// TAGS /////////////////////////////////
    Route::post('/admin/prodotti/tags/crea', ["before" => "csrf", "uses" => 'Prodotti\Controllers\TagsController@creaTag']);
    Route::post('/admin/prodotti/tags/associa', ["before" => "csrf", "uses" => 'Prodotti\Controllers\TagsController@associaTag']);
    //@todo add csrf ui
    Route::get('/admin/prodotti/tags/cancella', 'Prodotti\Controllers\TagsController@cancellaTag');
    ////////////////////// ACCESSORI PRODOTTO /////////////////////
    Route::get('/admin/accessori/list', ['as'=>'prodotti.accessori.list','uses' => 'Prodotti\Controllers\AccessoriController@lista']);
    Route::post('/admin/accessori/modifica', ['as'=>'prodotti.accessori.modifica', "before" => "csrf", 'uses' => 'Prodotti\Controllers\AccessoriController@postModifica']);
    Route::get('/admin/accessori/modifica', ['as'=>'prodotti.accessori.modifica','uses' => 'Prodotti\Controllers\AccessoriController@getModifica']);
    //@todo add csrf ui
    Route::any('/admin/accessori/cancella', ['as'=>'prodotti.accessori.cancella','uses' => 'Prodotti\Controllers\AccessoriController@cancella']);
    Route::post('/admin/prodotti/accessori/associa', ['as'=>'prodotti.accessori.associa', "before" => "csrf", 'uses' => 'Prodotti\Controllers\ProdottoController@associaAccessorio']);
    //@todo add csrf ui
    Route::any('/admin/prodotti/accessori/deassocia', ['as'=>'prodotti.accessori.deassocia','uses' => 'Prodotti\Controllers\ProdottoController@deassociaAccessorio']);

});