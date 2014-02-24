<?php  namespace Presenters\Traits; 

/**
 * Trait ViewHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
trait ViewHelper {

    /**
     * Controlla se il prodotto è stato fornito, in tal caso
     * setta il data toggle, altrimenti lo disabilita
     */
    public function get_toggle()
    {
        return $this->resource->exists ? 'data-toggle="tab"' : 'data-toggle="" disabled="disabled"';
    }

} 