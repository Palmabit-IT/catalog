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
        $this->null_resource = new $this->null_resource_name($this, $this->current_lang);
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
    public function getDescriptionRelationName()
    {
        return $this->descriptions_relation_name;
    }

    public function __get($key)
    {
        if(!$this->isDescriptionField($key))
        {
            return $this->resource->$key;
        }

        $current_description = $this->findCurrentDescription();

        return $current_description->$key;
    }

    public function __set($key, $value)
    {
        if(!$this->isDescriptionField($key))
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
        if(!$current_description) $current_description = $this->findDescriptionForLang($this->default_lang);
        $description_object = $current_description ? : $this->null_resource;

        return $description_object;
    }

    /**
     * @param $lang
     * @return mixed
     */
    protected function findDescriptionForLang($lang)
    {
        return $this->resource->language_descriptions->filter(function ($language_description) use ($lang)
        {
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
        $current_description = $this->findDescriptionForLang($this->current_lang);
        $description_object = $current_description ? : $this->null_resource;

        return $description_object->$key = $value;
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
    private function isDescriptionField($key)
    {
        return in_array($key, $this->resource->getDescriptionAttributes());
    }

    /**
     * @return mixed
     */
    public function getDescriptionObjects()
    {
        return $this->resource->getLanguageDescriptionsAttribute();
    }

    private function getAppLanguages()
    {
        $this->current_lang = L::get();
        $this->default_lang = L::getDefault();
    }

    public function removeLanguageDescription($lang)
    {
        foreach($this->resource->language_descriptions as $key => $language_description)
        {
            if($language_description->lang == $lang) unset($this->resource->language_descriptions[$key]);
        }
    }
}