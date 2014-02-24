<?php

/**
 * Ritorna $str se il link è attivo
 * @param String $url
 * @param String $active
 * @return $active
 */
function get_active($url, $active = 'active')
{
    return (Request::url() == $url) ? $active : '';
}
/**
 * Ottiene la select per le categorie
 * @return null
 */
function get_cat_select_arr()
{
    $cat_arr = [""];
    $cat = Categoria::whereLang(L::get_admin())
        ->get(["id","descrizione"])
        ->each(function($cat) use(&$cat_arr){
        $cat_arr[$cat->id] = $cat->descrizione;
    });
    return $cat_arr;
}

/**
 * Ottiene il menù attivo in base al nome associato alla route
 * si basa sul primo campo separato da punto
 */
function get_active_name($nome_match, $active = 'active')
{
    $nome_route = Route::currentRouteName();
    $nome_base = array_values(explode(".", $nome_route))[0];
    return (strcasecmp($nome_base,$nome_match) == 0) ? $active : '';
}

/**
 * Modifica la view del paginatore da usare
 */
function set_view_paginator($name)
{
$paginator = DB::connection()->getPaginator();
    $paginator->setViewName($name);
    DB::connection()->setPaginator($paginator);
}

/**
 * Ottiene i campo per la select ordina prodotti
 * @return array
 */
function get_select_ordine_arr()
{
    $arr = [];
    foreach(range(0,99) as $key)
    {
        $arr[$key] = $key;
    }
    return $arr;
}

/**
 * Ottiene i campo per la select accessori
 */
function get_select_accessori_arr()
{
    $select_arr = [];
    Accessori::all()->each(function($acc) use (&$select_arr){
        $select_arr[$acc->id] = $acc->descrizione;
    });
    return $select_arr;
}