<?php  namespace Palmabit\Catalog\ModelMultilanguage\Traits;

trait LanguageDescriptionsEditable{

    protected $language_descriptions_attribute = "language_descriptions";
    protected $language_descriptions_relation = "descriptions";

    public function getLanguageDescriptionsAttribute()
    {
        return $this->{$this->language_descriptions_attribute};
    }

    public function setLanguageDescriptionsAttribute($value)
    {
        $this->{$this->language_descriptions_attribute} = $value;
    }
} 