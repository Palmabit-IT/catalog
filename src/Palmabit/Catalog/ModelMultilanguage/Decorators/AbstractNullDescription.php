<?php  namespace Palmabit\Catalog\ModelMultilanguage\Decorators;

abstract class AbstractNullDescription
{

    public $lang;
    protected $decorator;
    protected $resource;
    protected $description_class = '';
    protected $id_field = '';

    public function __construct($decorator, $lang)
    {
        if(empty($this->description_class))
        {
            throw new \Exception("You need to set description_attribute field in your class");
        }
        if(empty($this->id_field))
        {
            throw new \Exception("You need to set description_attribute field in your class");
        }

        $this->decorator = $decorator;
        $this->resource = $this->decorator->getResource();
        $this->lang = $lang;
    }

    public function __get($key)
    {
        return '';
    }

    public function __set($key, $value)
    {
        $description_obj = new $this->description_class([$this->id_field => $this->resource->id]);
        $description_obj->lang = $this->lang;
        $description_obj->$key = $value;

        $this->decorator->getDescriptionObjects()->push($description_obj);
    }

    public function getResource()
    {
        return $this->resource;
    }
}