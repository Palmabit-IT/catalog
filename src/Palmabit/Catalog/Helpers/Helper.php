<?php namespace Palmabit\Catalog\Helpers;
/**
 * Class ImageHelper
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Input;
use Image;
use Palmabit\Library\Exceptions\NotFoundException;

class Helper {

    public static function getPathFromInput($nome_input)
    {
        if (Input::hasFile($nome_input))
        {
            return $path = Input::file($nome_input)->getRealPath();
        }
        else
        {
            throw new NotFoundException('File non trovato.');
        }
    }

    /**
     * Fetch an image given a path
     * @todo test
     */
    public static function getBinaryData($size = 600, $input_name)
    {
        return Image::make(static::getPathFromInput($input_name))->resize($size, null, true);
    }


} 