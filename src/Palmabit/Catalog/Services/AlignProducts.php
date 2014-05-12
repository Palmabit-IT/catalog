<?php  namespace Palmabit\Catalog\Services;

use Illuminate\Support\Facades\DB;
use Palmabit\Catalog\Models\Product;

/**
 * Class AlignProducts
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class AlignProducts 
{
    public function alignData()
    {
        // clean products with same lang and code duplicated
        $codes = $this->getDistincProductCodes();

        foreach($codes as $code)
        {
            $product = $this->getProduct($code);
            $this->updateAllProductSlugLang($product);
        }
    }

    public function cleanProducts()
    {
        $duplicated = $this->findDuplicatedRows();

        foreach($duplicated as $duplicate_row)
        {
            $total_rows = $duplicate_row->totals - 1;
            foreach(range(1, $total_rows) as $key)
            {
                $this->deleteAProductWithSameLangCode($duplicate_row);
            }
        }

    }
    
    /**
     * @return mixed
     */
    protected function getDistincProductCodes()
    {
        return DB::table('product')->select("code")->distinct()->get();
    }

    /**
     * @param $product
     */
    protected function updateAllProductSlugLang($product)
    {
        DB::table('product')
                ->where('code', '=', $product->code)
                ->update(["slug_lang" => ".$product->slug_lang."]);
    }

    /**
     * @param $code
     * @return mixed
     */
    protected function getProduct($code)
    {
        return Product::whereCode($code->code)->first();
    }

    /**
     * @return mixed
     */
    protected function findDuplicatedRows()
    {
        $duplicated = DB::select("select code, lang, COUNT(*) as totals from product group by code,lang having totals > 1");
        return $duplicated;
    }

    /**
     * @param $duplicate_row
     */
    protected function deleteAProductWithSameLangCode($duplicate_row)
    {
//        var_dump(Product::whereCode($duplicate_row->code)
//               ->whereLang($duplicate_row->lang)
//               ->first()->toArray());

        Product::whereCode($duplicate_row->code)
                ->whereLang($duplicate_row->lang)
                ->first()
                ->delete();
    }
} 