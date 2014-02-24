<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableProduct extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if(! Schema::hasTable('product'))
            Schema::create('product', function(Blueprint $table) {
			$table->increments('id');
            $table->string('code')->nullable();
            // for single table inheritance
            $table->string('type');
            $table->string('name');
            $table->string('slug')->unique();
			$table->text('description');
            $table->text('long_description');
            $table->boolean('featured');
            $table->integer("order")->default(0);
            // for multilanguage
            $table->string('slug_lang');
            $table->string('lang',2)->default('it');
            $table->timestamps();
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
        Schema::dropIfExists('product');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
