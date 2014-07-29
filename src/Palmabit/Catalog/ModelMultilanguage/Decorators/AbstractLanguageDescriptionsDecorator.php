<?php  namespace Palmabit\Catalog\ModelMultilanguage\Decorators;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use L;
use Exception;
use Palmabit\Catalog\ModelMultilanguage\Interfaces\DecoratorInterface;
use Palmabit\Catalog\ModelMultilanguage\Interfaces\EditableLanguageDescriptionInterface;

abstract class AbstractLanguageDescriptionsDecorator implements DecoratorInterface
{

    protected $null_resource_name = '';
    protected $descriptions_relation_name = '';
    protected $resource;
    protected $null_resource;
    protected $current_lang;
    protected $default_lang;
    /**
     * The description object used with lazy loading
     *
     * @var
     */
    protected $description_object;

    public function __construct(EditableLanguageDescriptionInterface $resource)
    {
        if(empty($this->descriptions_relation_name))
        {
            throw new Exception("You cannot instantiate this class without setting descriptions_relation_name field.");
        }

        if(empty($this->null_resource_name))
        {
            throw new Exception("You cannot instantiate this class without setting null_resource_name field.");
        }

        $this->getAppLanguages();
        $this->resource = $resource;
        $this->initializeLanguageDescriptions();
        $this->null_resource = new $this->null_resource_name($this, $this->default_lang);
    }

    private function initializeLanguageDescriptions()
    {
        $this->resource->language_descriptions = $this->resource->{$this->descriptions_relation_name}()->get();
    }

    /**
     * @param mixed $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getDescriptionFieldName()
    {
        return $this->descriptions_relation_name;
    }

    public function __get($key)
    {
        if($this->hasFillable($key))
        {
            return $this->resource->$key;
        }

        $current_description = $this->findCurrentDescription();

        return $current_description->$key;
    }

    public function __set($key, $value)
    {
        if(property_exists($this, $key))
        {
            return $this->$key = $value;
        }

        if(property_exists($this->resource, $key))
        {
            return $this->resource->$key = $value;
        }

        $this->setDescriptionValue($key, $value);
    }

    /**
     * @param $current_lang
     * @return null
     */
    public function findCurrentDescription()
    {
        $current_description = $this->findDescriptionForLang($this->current_lang);
        if(! $current_description) $current_description = $this->findDescriptionForLang($this->default_lang);
        $this->description_object = $current_description ?: $this->null_resource;

        return $this->description_object;
    }

    /**
     * @param $lang
     * @return mixed
     */
    protected function findDescriptionForLang($lang)
    {
        return $this->resource->language_descriptions->filter(function ($language_description) use($lang) {
            return ($language_description->lang == $lang) ? true : false;
        })->first();
    }

    /**
     * @param $key
     * @param $value
     * @return null
     */
    protected function setDescriptionValue($key, $value)
    {
        $description = $this->findCurrentDescription($this->default_lang);
        $description->$key = $value;
        return $description;
    }

    /**
     * @param       $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, array $params = [])
    {
        // Calls method on resource if exists
        if(method_exists($this->resource, $method))
        {
            return call_user_func_array([$this->resource, $method], $params);
        }
    }

    /**
     * @param $key
     * @return bool
     */
    private function hasFillable($key)
    {
        return in_array($key, $this->resource->getFillable());
    }

    /**
     * @return mixed
     */
    public function getDescriptionObject()
    {
        return $this->description_object;
    }

    private function getAppLanguages()
    {
        $this->current_lang = L::get();
        $this->default_lang = L::getDefault();
    }
}