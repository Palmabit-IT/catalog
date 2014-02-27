<?php namespace Palmabit\Catalog\Repository;
/**
 * Class EloquentProductImageRepository
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */

use Palmabit\Catalog\Models\ProductImage;
use Palmabit\Library\Repository\EloquentBaseRepository;
use Palmabit\Library\Repository\Interfaces\RepositoryInterface;
use Palmabit\Catalog\Helpers\Helper as ImageHelper;
use Palmabit\Library\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB, Image;


class EloquentProductImageRepository extends EloquentBaseRepository{

    public function __construct()
    {
      return parent::__construct(new ProductImage) ;
    }

    /**
     * Creates un model
     *
     * @override
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create([
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
     * @todo test
     */
    public function changeFeatured($id, $product_id)
    {
        DB::connection()->getPdo()->beginTransaction();
        try
        {
            //clear old featured image
            $this->model->where('product_id','=',$product_id)
                ->get()
                ->each(function($img){
                    $this->update($img->id, ["featured" => 0]);
                });
            // set new featured image
            $this->update($id, ["featured" => 1]);
        }
        catch(ModelNotFoundException $e)
        {
            DB::connection()->getPdo()->rollBack();
            throw new NotFoundException("Prodotto non trovato.");
        }
        DB::connection()->getPdo()->commit();
    }
}