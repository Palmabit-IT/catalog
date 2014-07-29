<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDescription extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('product_description'))
        {
            Schema::create('product_description', function (Blueprint $table)
            {
                $table->increments('id');
                $table->string('name');
                $table->string('slug')->nullable();
                $table->text('description', 60);
                $table->text('long_description')->nullable();
                $table->string('lang', 2)->default('it');
                $table->integer('product_id')->unsigned();
                // indexes
                $table->unique(array('slug', 'lang'));
                // eloquent timestamps
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
