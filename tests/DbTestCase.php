<?php  namespace Palmabit\Catalog\Tests;
/**
 * Class DbTestCase
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Artisan;
use DB;
class DbTestCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $artisan = $this->app->make( 'artisan' );

        $this->populateDB($artisan);
    }

    /**
     * @test
     **/
    public function it_mock_test()
    {
        $this->assertTrue(true);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $sqlite_conn = array(
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'prefix'    => '',
        );

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', $sqlite_conn);
    }

    /**
     * @param $artisan
     */
    protected function populateDB($artisan)
    {
        $artisan->call('migrate', [
                                  "--database" => "testbench", '--path' => '../src/migrations', '--seed' => '']);
    }
} 