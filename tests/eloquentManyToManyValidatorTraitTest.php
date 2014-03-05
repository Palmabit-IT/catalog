<?php  namespace Palmabit\Catalog\Tests;
use Illuminate\Support\Facades\DB;
use Palmabit\Catalog\Validators\Traits\eloquentManyToManyUniqueValidatorTrait;
use Mockery as m;
/**
 * Test eloquentManyToManyValidatorTraitTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class eloquentManyToManyValidatorTraitTest extends \PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_validate_for_existings_many_to_many_relations()
    {
        $stub = new eloquentManyToManyUniqueValidatorTraitStub();
        $count_mock = m::mock('StdClass')->shouldReceive('count')->once()->andReturn(0)->getMock();
        $where_2_mock = m::mock('StdClass')->shouldReceive('where')->once()->andReturn($count_mock)->getMock();
        $where_mock = m::mock('StdClass')->shouldReceive('where')->once()->andReturn($where_2_mock)->getMock();
        DB::shouldReceive('table')->once()->andReturn($where_mock);
        $stub->validateExistence("1","2","t","n1","n2");
    }

    /**
     * @test
     * @expectedException \Palmabit\Library\Exceptions\ValidationException
     **/
    public function it_throw_exception_if_found_an_aready_existings_relation()
    {
        $stub = new eloquentManyToManyUniqueValidatorTraitStub();
        $count_mock = m::mock('StdClass')->shouldReceive('count')->once()->andReturn(1)->getMock();
        $where_2_mock = m::mock('StdClass')->shouldReceive('where')->once()->andReturn($count_mock)->getMock();
        $where_mock = m::mock('StdClass')->shouldReceive('where')->once()->andReturn($where_2_mock)->getMock();
        DB::shouldReceive('table')->once()->andReturn($where_mock);
        $stub->validateExistence("1","2","t","n1","n2");
    }
}

class eloquentManyToManyUniqueValidatorTraitStub
{
    use eloquentManyToManyUniqueValidatorTrait;
}