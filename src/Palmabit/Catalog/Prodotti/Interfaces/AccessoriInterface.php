<?php namespace Prodotti\Interfaces;
/**
 * Interface AccessoriInterface
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
interface AccessoriInterface 
{
    /**
     * Ottiene gli accessori collegati
     * @param $id
     * @return mixed
     */
    public function getAccessori($id);

    /**
     * Associa gli accessori
     * @param $elemento_id
     * @param $accessorio_id
     * @return mixed
     */
    public function associaAccessorio($elemento_id, $accessorio_id);
} 