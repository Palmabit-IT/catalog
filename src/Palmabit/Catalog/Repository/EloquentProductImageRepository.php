i<?php namespace Palmabit\Catalog\Repository;
/**
 * Class EloquentProductImageRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */

use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Library\Repository\Interfaces\RepositoryInterface;
use Palmabit\Catalog\Helpers\Helper as ImageHelper;
use Palmabit\Library\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;


class EloquentProductImageRepository extends EloquentBaseRepository{

    protected $model_name = 'Palmabit\Catalog\Models\ProductImage';

    /**
     * Fetch an image given a path
     */
    protected function getBinaryData()
    {
        return Image::make(ImageHelper::getPathFromInput('image'))->resize(600, null, true);
    }

    /**
     * Cange featured image
     *
     * @param $id
     * @param $product_id
     */
    public function changeFeatured($id, $product_id)
    {
        $model = $this->model_name;
        DB::connection()->getPdo()->beginTransaction();
        try
        {
            //clear old featured image
            $model::where('product_id','=',$product_id)
                ->get()
                ->each(function($img){
                    $this->update($img->id, ["in_evidenza" => 0]);
                });
            // set new featured image
            $this->update($id, ["in_evidenza" => 1]);
        }
        catch(ModelNotFoundException $e)
        {
            DB::connection()->getPdo()->rollBack();
            throw new NotFoundException("Prodotto non trovato.");
        }
        DB::connection()->getPdo()->commit();
    }
}