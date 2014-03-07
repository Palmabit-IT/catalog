<?php namespace Palmabit\Catalog\Presenters;
/**
 * Class PresenterCategory
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\Presenters\Interfaces\ProductCategoryPresenterInterface;
use Palmabit\Catalog\Traits\ViewHelper;
use Palmabit\Library\Presenters\AbstractPresenter;

class PresenterCategory extends AbstractPresenter implements ProductCategoryPresenterInterface{
 use ViewHelper;

    /**
     * @todo test
     */
    public function image()
    {
        return $this->resource->image ? "data:image;base64,{$this->resource->image}" : null;
    }

    public function featured_image()
    {
        return $this->image();
    }

    public function description()
    {
        return $this->resource->description;
    }

    public function name()
    {
        return $this->resource->name;
    }
}