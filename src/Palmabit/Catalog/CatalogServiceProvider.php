<?php namespace Palmabit\Catalog;

use Illuminate\Support\ServiceProvider;
use Palmabit\Catalog\Repository\EloquentOrderRepository;
use Palmabit\Catalog\Repository\EloquentProductRepository;
use Palmabit\Catalog\Repository\EloquentProductImageRepository;
use Palmabit\Catalog\Repository\EloquentCategoryRepository;
use Illuminate\Foundation\AliasLoader;

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
        // include custom validators
        require __DIR__ . "/../../validators.php";
        // various includes
        require __DIR__ . "/../../includes.php";
        // event subscribers
        require __DIR__ . "/../../events.php";

        $this->bindRepositories();
	}

    protected function bindRepositories()
    {
        $this->app->bind('category_repository', function ($app, $is_admin) {
            return new EloquentCategoryRepository($is_admin);
        });

        $this->app->bind('product_repository', function ($app, $is_admin) {
            return new EloquentProductRepository($is_admin);
        });

        $this->app->bind('product_image_repository', function ($app) {
            return new EloquentProductImageRepository();
        });

        $this->app->bind('order_repository', function ($app) {
            return new EloquentOrderRepository();
        });
    }

    protected function loadOtherProviders()
    {
        $this->app->register('Intervention\Image\ImageServiceProvider');
        $this->app->register('Palmabit\Multilanguage\MultilanguageServiceProvider');
        $this->app->register('Palmabit\Authentication\AuthenticationServiceProvider');
    }

    protected function registerAliases()
    {
        AliasLoader::getInstance()->alias("Image", 'Intervention\Image\Facades\Image');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->loadOtherProviders();
        $this->registerAliases();
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