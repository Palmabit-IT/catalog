<?php namespace Prodotti\Helper;
/**
 * Class ImageHelper
 *
 * @author jacopo beschi
 */
use Input;
use Exceptions\NotFoundException;

class ImageHelper {

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
} 