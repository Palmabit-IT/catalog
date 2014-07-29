<?php  namespace Palmabit\Catalog\ModelMultilanguage\Interfaces;

interface DecoratorInterface {
    /**
     * @return mixed
     */
    public function getResource();

    public function __construct(EditableLanguageDescriptionInterface $resource);

    /**
     * @param mixed $resource
     */
    public function setResource($resource);
}