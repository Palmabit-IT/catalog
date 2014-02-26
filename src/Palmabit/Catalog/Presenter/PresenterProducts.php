<?php namespace Palmabit\Catalog\Presenters;
/**
 * Class PresenterProducts
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Palmabit\Catalog\Models\ProductImage;
use Palmabit\Catalog\Traits\ViewHelper;
use Palmabit\Library\Presenters\AbstractPresenter;

class PresenterProducts extends AbstractPresenter {
use ViewHelper;

    protected $default_img_path;

    public function __construct($resource)
    {
        $this->default_img_path = public_path()."/packages/catalog/img/no-photo.png";
        return parent::__construct($resource);
    }
    /**
     * Obtains featured image
     * @return null
     * @return String|null
     */
    public function features()
    {
        $featured = $this->resource->product_images()
            ->where("featured","=",1)
            ->get();

        if($featured->isEmpty())
            return ["data" => "data:image;base64,".base64_encode(ProductImage::getImageFromUrl($this->default_img_path) ), "alt" => ""];
        else $featured = $featured->first();

        return array("data"=> $featured ? "data:image;base64,{$featured->data}" : null, "alt" => $featured->descrizione);
    }

    public function images_all()
    {
        return $this->images(false);
    }

    public function immages_no_featured()
    {
        return $this->images(true);
    }

    /**
     * Obtain product images
     * @param $escludi_evidenza
     * @return String
     */
    protected function images($exclude_featured = true)
    {
        $all_img = array();

        $images = $this->resource->product_images();
        if($exclude_featured)
            $images = $images->where('in_evidenza','=',0);
        $images = $images->get();

        $images->each(function($image) use(&$all_img){
            $all_img[] = [
                "data" => "data:image;base64,{$image->data}",
                "alt" => $image->description,
                "id" => $image->id,
                "featured" => $image->featured
            ];
        });

        return $all_img;
    }

    public function categories()
    {
        $cat = $this->resource->categories()->get();
        if(! $cat->isEmpty()) $cat = $cat->first();

        return (isset($cat->description)) ? $cat->description : '';
    }

    public function categories_ids()
    {
        $cat = $this->resource->categories()->get();
        if(! $cat->isEmpty()) $cat = $cat->first();

        return (isset($cat->id)) ? $cat->id: '';
    }
} 