<?php  namespace Palmabit\Catalog\Tests;

use Palmabit\Catalog\ModelMultilanguage\Decorators\AbstractNullDescription;

class AbstractNullDescriptionTest extends \PHPUnit_Framework_TestCase  {

  /**
   * @test
   * @expectedException \Exception
   **/
  public function requiresDescriptionFieldNameToBeIstantiated()
  {
    new NullDescriptionStubError(new NullDecoratorStub() ,'lang');
  }
  
}

class NullDescriptionStubError extends AbstractNullDescription
{}

class NullDecoratorStub {
  public function getResource()
  {
    return null;
  }
  public function getDescriptionFieldName()
  {
    return null;
  }
}