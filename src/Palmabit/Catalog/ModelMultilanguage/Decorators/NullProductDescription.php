<?php  namespace Palmabit\Catalog\ModelMultilanguage\Decorators;

class NullProductDescription extends AbstractNullDescription {
    protected $description_class = '\Palmabit\Catalog\Models\ProductDescription';
    protected $id_field = "product_id";
}