<?php  namespace Palmabit\Catalog\ModelMultilanguage\Decorators;
use L;

class ProductLanguageDecorator extends AbstractLanguageDescriptionsDecorator {
    protected $null_resource_name = 'Palmabit\Catalog\ModelMultilanguage\Decorators\NullProductDescription';
    protected $descriptions_relation_name = 'descriptions';
}