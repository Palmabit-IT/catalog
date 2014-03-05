<?php namespace Palmabit\Catalog\Tests;
/**
 * Test TestCase
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class TestCase extends \Orchestra\Testbench\TestCase  {

    public function setUp()
    {
        parent::setUp();

        require_once __DIR__ . "/../src/routes.php";
    }

    protected function getPackageProviders()
    {
        return [
                'Palmabit\Catalog\CatalogServiceProvider',
            ];
    }

}
 