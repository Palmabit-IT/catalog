<?php namespace Category\ServiceProvider;
/**
 * Class CategoryServiceProvider
 *
 * Usato per effettuare operazioni generiche alle categorie
 *
 * @package Auth
 * @author jacopo beschi
 */

use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        include_once __DIR__ . "/../routes.php";
    }
} 