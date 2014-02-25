<?php namespace Palmabit\Catalog;

use Illuminate\Support\ServiceProvider;
use Palmabit\Catalog\Repository\EloquentCategoryRepository;
use Palmabit\Catalog\Repository\EloquentProductRepository;
use Palmabit\Catalog\Repository\EloquentProductImageRepository;

class CatalogServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('palmabit/catalog');

        // include filters
        require __DIR__ . "/../../filters.php";
        // include routes.php
        require __DIR__ . "/../../routes.php";
        // include view composers
        require __DIR__ . "/../../composers.php";

        $this->bindRepositories();
	}

    protected function bindRepositories()
    {
        $this->app->bind('category_repository', function ($app, $is_admin) {
            return new EloquentCategoryRepository;
        });

        $this->app->bind('product_repository', function ($app, $is_admin) {
            return new EloquentProductRepository;
        });

        $this->app->bind('product_image_repository', function ($app) {
            return new EloquentProductImageRepository;
        });
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}