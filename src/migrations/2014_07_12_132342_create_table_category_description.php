<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCategoryDescription extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if(! Schema::hasTable('category_description'))
            Schema::create('category_description', function(Blueprint $table) {
                $table->increments('id');
                $table->string('description');
                $table->string('slug')->nullable();
                $table->timestamps();
                // for language handling
                $table->string('slug_lang');
                $table->string('lang',2)->default('it');
                $table->unique(array('slug_lang', 'lang'));
                // relations
                $table->integer('category_id')->unsigned();
                $table->foreign('category_id')
                      ->references('id')->on('category')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('category_description');
    }

}
