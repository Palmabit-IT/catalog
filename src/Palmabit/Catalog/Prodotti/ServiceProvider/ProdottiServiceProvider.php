<?php namespace Prodotti\ServiceProvider;
/**
 * Class ProdottiServiceProvider
 *
 * @package Auth
 * @author jacopo beschi
 */

use Illuminate\Support\ServiceProvider;

class ProdottiServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        include_once __DIR__ . "/../routes.php";
    }
} 