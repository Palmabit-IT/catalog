<?php namespace Palmabit\Catalog\Presenters;
/**
 * Class PresenterProducts
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Config, App, URLT;
use Palmabit\Catalog\Models\ProductImage;
use Palmabit\Catalog\Presenters\Interfaces\ProductCategoryPresenterInterface;
use Palmabit\Catalog\Traits\ViewHelper;
use Palmabit\Authentication\Exceptions\GroupNotFoundException;
use Palmabit\Library\Presenters\AbstractPresenter;

class PresenterProducts extends AbstractPresenter implements ProductCategoryPresenterInterface{
use ViewHelper;

    protected $default_img_path;

    public function __construct($resource)
    {
        $this->default_img_path = public_path()."/packages/palmabit/catalog/img/no-photo.png";
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
        return (! $cat->isEmpty()) ? $cat->all(): null;
    }

    /**
     * @return string
     * @deprecated
     */
    public function categories_ids()
    {
        $cat = $this->resource->categories()->get();
        if(! $cat->isEmpty()) $cat = $cat->first();

        return (isset($cat->id)) ? $cat->id: '';
    }

    public function accessories()
    {
        $prod = $this->resource->accessories()->get();
        return (! $prod->isEmpty()) ? $prod->all(): null;
    }

    /**
     * Obtain the price if you buy low quantity or if quantity handling is not enabled
     * @return mixed
     * @todo add the test for groupnotfoundexception
     */
    public function price_small()
    {
        $group_professional = Config::get('catalog::groups.professional_group_name');
        $group_logged = Config::get('catalog::groups.logged_group_name');
        $authenticator = App::make('authenticator');

        // if not logged no price
        if ( ! $authenticator->check()) return '';

        try
        {
            if ( $authenticator->hasGroup($group_professional)) return $this->resource->price2;
            elseif ( $authenticator->hasGroup($group_logged)) return $this->resource->price1;
        }
        // if doesn't find any of the groups
        catch(GroupNotFoundException $e) {}

        return '';
    }

    /**
     * Obtain the price if quantity handling is enabled and you buy more than quantity_pricing_quantity
     */
    public function price_big()
    {
        $group_professional = Config::get('catalog::groups.professional_group_name');
        $group_logged = Config::get('catalog::groups.logged_group_name');
        $authenticator = App::make('authenticator');

        // if not logged no price
        if ( ! $authenticator->check()) return '';

        try
        {
            if ($this->resource->quantity_pricing_enabled)
            {
                if($authenticator->hasGroup($group_professional)) return $this->resource->price3;
                elseif($authenticator->hasGroup($group_logged)) return $this->resource->price2;
            }
            else
            {
                return $this->price_small();
            }
        }
        // if doesn't find any of the groups
        catch(GroupNotFoundException $e) {}

        return '';
    }

    public function featured_image()
    {
        return $this->features();
    }

    public function description()
    {
        return substr($this->resource->description,0,60);
    }

    public function name()
    {
        return $this->resource->name;
    }

    public function urlVideo()
    {
        return preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>",$this->resource->video_link);
    }

    public function getLink()
    {
        return URLT::action('ProductController@show', ['slug_lang' => $this->resource->slug_lang] );
    }
}