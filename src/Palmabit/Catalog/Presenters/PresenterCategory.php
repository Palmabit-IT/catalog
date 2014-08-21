<?php namespace Palmabit\Catalog\Presenters;
/**
 * Class PresenterCategory
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\Models\ProductImage;
use Palmabit\Catalog\Presenters\Interfaces\ProductCategoryPresenterInterface;
use Palmabit\Catalog\Traits\ViewHelper;
use Palmabit\Library\Presenters\AbstractPresenter;
use L, URLT;

class PresenterCategory extends AbstractPresenter implements ProductCategoryPresenterInterface{
 use ViewHelper;

    protected $default_img_path;

    public function __construct($resource)
    {
        $this->default_img_path = public_path()."/packages/palmabit/catalog/img/no-photo.png";
        return parent::__construct($resource);
    }

    /**
     * @return array
     * @todo refactor to test for default image: no statics
     */
    public function image()
    {
        $data = $this->resource->image ? "data:image;base64,{$this->resource->image}" : "data:image;base64,".base64_encode(ProductImage::getImageFromUrl($this->default_img_path) );
        $alt = $this->resource->description;

        return ["data" => $data, "alt" => $alt];
    }

    public function featured_image()
    {
        return $this->image();
    }

    public function name()
    {
        return $this->resource->name;
    }

    public function siblings()
    {
        return $this->resource->siblings()->whereLang(L::get())->get();
    }

    public function getLink()
    {
      return ($description = $this->getDescriptionObjectOfLang(L::get())) ? URLT::action('CategoryController@show', ['slug' => $description->slug] ) : '#';
    }

    public function getDescriptionObjectOfLang($lang)
    {
        return $this->resource->category_description()->whereLang($lang)->first();
    }
}