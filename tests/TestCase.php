<?php namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\Tests\Stubs\NullLogger;

/**
 * Test TestCase
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class TestCase extends \Orchestra\Testbench\TestCase  {

    public function setUp()
    {
        parent::setUp();

        $this->useMailPretend();
        $this->useNullLogger();

        require_once __DIR__ . "/../src/routes.php";
    }

    protected function getPackageProviders()
    {
        return [
                'Palmabit\Catalog\CatalogServiceProvider',
            ];
    }

    public function useNullLogger()
    {
        \Mail::setLogger(new NullLogger());
    }

    protected function useMailPretend()
    {
        \Config::set('mail.pretend', true);
    }
}
 