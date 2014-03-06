<?php namespace Palmabit\Catalog\Presenters;
/**
 * Class PresenterCategory
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\Traits\ViewHelper;
use Palmabit\Library\Presenters\AbstractPresenter;

class PresenterCategory extends AbstractPresenter {
 use ViewHelper;

    /**
     * @todo test
     */
    public function image()
    {
        return $this->resource->image ? "data:image;base64,{$this->resource->image}" : null;
    }
} 