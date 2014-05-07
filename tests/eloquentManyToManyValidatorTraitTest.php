<?php  namespace Palmabit\Catalog\Tests;

use DB;
use Palmabit\Catalog\Validators\Traits\eloquentManyToManyUniqueValidatorTrait;
use Mockery as m;
/**
 * Test eloquentManyToManyValidatorTraitTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class eloquentManyToManyValidatorTraitTest extends \PHPUnit_Framework_TestCase {

    protected $eloquentManyToManyUniqueValidatorTraitStub;

    public function setUp()
    {
        $this->eloquentManyToManyUniqueValidatorTraitStub = new eloquentManyToManyUniqueValidatorTraitStub();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_validate_for_existings_many_to_many_relations()
    {
        $number_results = 0;
        $where_mock = $this->checkForExistenceMock($number_results);
        DB::shouldReceive('table')->once()->andReturn($where_mock);
        $this->eloquentManyToManyUniqueValidatorTraitStub->validateExistence("1","2","t","n1","n2");
    }

    /**
     * @test
     * @expectedException \Palmabit\Library\Exceptions\ValidationException
     **/
    public function it_throw_exception_if_found_an_aready_existings_relation()
    {
        $number_results = 1;
        $where_mock = $this->checkForExistenceMock($number_results);
        DB::shouldReceive('table')->once()->andReturn($where_mock);
        $this->eloquentManyToManyUniqueValidatorTraitStub->validateExistence("1","2","t","n1","n2");
    }

    /**
     * @return m\MockInterface
     */
    private function checkForExistenceMock($number_results)
    {
        $count_mock   = m::mock('StdClass')->shouldReceive('count')->once()->andReturn($number_results)->getMock();
        $where_2_mock = m::mock('StdClass')->shouldReceive('where')->once()->andReturn($count_mock)->getMock();
        $where_mock   = m::mock('StdClass')->shouldReceive('where')->once()->andReturn($where_2_mock)->getMock();
        return $where_mock;
    }
}

class eloquentManyToManyUniqueValidatorTraitStub
{
    use eloquentManyToManyUniqueValidatorTrait;
}