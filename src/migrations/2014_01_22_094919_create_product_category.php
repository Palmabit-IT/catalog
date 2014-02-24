<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductCategory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if(! Schema::hasTable('product_category'))
            Schema::create('product_category', function(Blueprint $table) {
			$table->increments('id');
            $table->integer("product_id")->unsigned();
            $table->integer("category_id")->unsigned();
            // foreign keys
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('category_id')
                ->references('id')->on('category')
                ->onUpdate('cascade')
                ->onDelete('cascade');
		});
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
        Schema::dropIfExists('product_category');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
