<?php namespace Palmabit\Catalog\Helpers;
/**
 * Class ImageHelper
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
use Input;
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
} 