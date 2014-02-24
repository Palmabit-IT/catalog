<?php
use Palmabit\Catalog\Category;

/**
 * Obtain the category select
 * @return null
 */
function get_cat_select_arr()
{
    $cat_arr = [""];
    Category::whereLang(L::get_admin())
        ->get(["id","descrizione"])
        ->each(function($cat) use(&$cat_arr){
        $cat_arr[$cat->id] = $cat->descrizione;
    });
    return $cat_arr;
}

/**
 * Obtain the order select
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
 * Sets another view paginator
 */
function set_view_paginator($name)
{
$paginator = DB::connection()->getPaginator();
    $paginator->setViewName($name);
    DB::connection()->setPaginator($paginator);
}