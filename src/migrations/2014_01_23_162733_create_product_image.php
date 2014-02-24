<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductImage extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if(! Schema::hasTable('product_image'))
        {
            Schema::create('product_image', function(Blueprint $table) {
			$table->increments('id');
			$table->string('description')->nullable();
            $table->integer("product_id")->unsigned()->nullable();
            $table->boolean('featured')->default(false);
			$table->timestamps();
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onUpdate('cascade')
                ->onDelete('cascade');
		    });
            DB::statement('ALTER TABLE  `product_image` ADD  `data` LONGBLOB NOT NULL');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('product_image');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
