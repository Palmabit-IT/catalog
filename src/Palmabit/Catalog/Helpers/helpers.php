<?php
use Palmabit\Catalog\Models\Category;
use Palmabit\Catalog\Models\Product;

/**
 * Obtain the product select
 * @return null
 */
if ( ! function_exists('get_product_select_arr'))
{
    function get_product_select_arr()
    {
        $products = Product::get();
        $arr_prods = [];
        foreach($products as $product)
        {
            $arr_prods[] = [
                "name" => $product->decorateLanguage(L::get_admin())->name,
                "id" => $product->id
            ];
        }

        return $arr_prods;
    }
}

/**
 * Obtain the category select
 * @return null
 */
if ( ! function_exists('get_cat_select_arr'))
{
    function get_cat_select_arr($with_empty_field = false)
    {
        $cat_arr = $with_empty_field ? ["0"=>""] : [];
        Category::get(["id","name"])
            ->each(function($cat) use(&$cat_arr){
            $cat_arr[$cat->id] = $cat->name;
        });
        return $cat_arr;
    }
}

/**
 * Obtain the optins for the shippping_select
 */
if ( ! function_exists('get_shipping_select_arr'))
{
    function get_shipping_select_arr($with_empty_field = false)
    {
        $arr = $with_empty_field ? [""=>""] : [];
        $arr = array_merge($arr, Config::get('catalog::shipping_nations') );
        return $arr;
    }
}

/**
 * Obtain the order select
 * @return array
 */
if ( ! function_exists('get_select_order_arr'))
{
    function get_select_order_arr()
    {
        $arr = [];
        foreach(range(0,99) as $key)
        {
            $arr[$key] = $key;
        }
        return $arr;
    }
}

/**
 * Sets another view paginator
 */

if ( ! function_exists('set_view_paginator'))
{
    function set_view_paginator($name)
    {
    $paginator = DB::connection()->getPaginator();
        $paginator->setViewName($name);
        DB::connection()->setPaginator($paginator);
    }
}