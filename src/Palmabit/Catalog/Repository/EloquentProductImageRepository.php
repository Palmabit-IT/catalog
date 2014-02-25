<?php namespace Palmabit\Catalog\Repository;
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
use DB, Image;


class EloquentProductImageRepository extends EloquentBaseRepository{

    protected $model_name = 'Palmabit\Catalog\Models\ProductImage';

    /**
     * Creates un model
     *
     * @override
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $model = $this->model_name;
        return $model::create([
                                        "description" => $data["description"],
                                        "product_id" => $data["product_id"],
                                        "featured" => $data["featured"],
                                        "data" => $this->getBinaryData()
                                        ]);
    }


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