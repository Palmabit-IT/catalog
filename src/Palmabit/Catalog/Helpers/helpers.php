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
        return Product::whereLang(L::get_admin())
                    ->lists('name', 'id');
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
        Category::whereLang(L::get_admin())
            ->get(["id","description"])
            ->each(function($cat) use(&$cat_arr){
            $cat_arr[$cat->id] = $cat->description;
        });
        return $cat_arr;
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