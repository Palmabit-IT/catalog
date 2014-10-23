<?php  namespace Palmabit\Catalog\Tests;

/**
 * Class DbTestCase
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Artisan;
use BadMethodCallException;
use DB;
use Illuminate\Support\Collection;

class DbTestCase extends TestCase
{
    protected $times = 1;

    protected $faker;

    public function setUp()
    {
        parent::setUp();
        $this->setMailPretend();

        $artisan = $this->app->make('artisan');
        $this->populateDB($artisan);
        $this->faker = \Faker\Factory::create();
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
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $sqlite_conn = array(
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        );

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', $sqlite_conn);
    }

    /**
     * @param       $class_name
     * @param mixed $extra
     * @return array
     */
    protected function make($class_name, $extra = [])
    {
        $created_objs = new Collection();

        while ($this->times--) {
            $extra_data = ($extra instanceof \Closure) ? $extra() : $extra;
            $stub_data = array_merge($this->getModelStub(), $extra_data);
            $created_objs->push($class_name::create($stub_data));
        }

        $this->resetTimes();

        return $created_objs;
    }

    protected function getModelStub()
    {
        throw new BadMethodCallException("You need to implement getModelStub method in your own test class.");
    }

    protected function times($count)
    {
        $this->times = $count;

        return $this;
    }

    protected function resetTimes()
    {
        $this->times = 1;
    }

    protected function assertObjectHasAllAttributes(array $attributes, $object, array $except = [])
    {
        $this->objectHasAllArrayAttributes($attributes, $object, $except);
    }

    protected function objectHasAllArrayAttributes(array $attributes, $object, array $except = [])
    {
        foreach ($attributes as $key => $value) {
            if (!in_array($key, $except)) $this->assertEquals($value, $object->$key, "L'oggetto ".get_class($object)." ha {$key} diverse: {$value}//{$object->$key}");
        }
    }

    /**
     * @param $artisan
     */
    protected function populateDB($artisan)
    {
        $artisan->call('migrate', ["--database" => "testbench", '--path' => '../src/migrations', '--seed' => '']);
    }

    protected function setMailPretend()
    {
        \Config::set('mail.pretend', true);
    }
}
