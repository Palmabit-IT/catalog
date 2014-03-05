<?php  namespace Palmabit\Catalog\Validators\Traits;

use DB;
use Illuminate\Support\MessageBag;
use Palmabit\Library\Exceptions\ValidationException;

/**
 * Class ManyToManyUniqueValidator
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
Trait eloquentManyToManyUniqueValidatorTrait
{
    public function validateExistence($field_1, $field_2, $table_name, $field_1_name, $field_2_name)
    {
        $occurrence  = DB::table($table_name)->where($field_1_name, '=', $field_1)->where($field_2_name, '=', $field_2)->count();

        if($occurrence != 0)
        {
            $this->errors = new MessageBag(["model" => "L'entità è già stata associata"]);
            throw new ValidationException;
        }
    }
} 