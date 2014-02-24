<?php namespace Palmabit\Catalog\Traits;

/**
 * Trait ViewHelper
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
trait ViewHelper {

    /**
     * Used to enable subtabs after product creation
     */
    public function get_toggle()
    {
        return $this->resource->exists ? 'data-toggle="tab"' : 'data-toggle="" disabled="disabled"';
    }
} 