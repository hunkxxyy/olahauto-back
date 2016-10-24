<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDragableMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dragable_menu', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('parent_id');
            $table->integer('top')->default(0);
            $table->string('name', 500);
            $table->string('route', 500);
            $table->text('description')->nullable();;
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dragable_menu');
    }
}
