<?php  namespace Palmabit\Catalog\ModelMultilanguage\Traits;

trait LanguageDescriptionsEditable
{

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

    /**
     * @return string
     */
    public function getLanguageDescriptionsRelation()
    {
        return $this->language_descriptions_relation;
    }

    /**
     * @return array
     */
    public function getDescriptionAttributes()
    {
        return $this->description_attributes;
    }

    public function save(array $options = [])
    {
        $this->deleteAllDescriptions();

        foreach($this->getLanguageDescriptionsAttribute() as $attribute)
        {
            $this->forceInsertAttribute($attribute);
        }

        return parent::save($options);
    }

    public function deleteAllDescriptions()
    {
        $this->{$this->language_descriptions_relation}()->delete();
    }

    /**
     * @param $attribute
     */
    private function forceInsertAttribute($attribute)
    {
        $attribute->exists = false;
        $attribute->id = null;
        $attribute->save();
    }
}