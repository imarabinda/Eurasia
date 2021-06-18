<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCutPiecesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cut_pieces', function (Blueprint $table) {
            $table->id();
            $table->integer('pieces');

            $table->unsignedBigInteger('fabric_type_id');
            
            $table->foreign('fabric_type_id')
                ->references('id')
                ->on('fabric_types')
                ->onDelete('cascade');
            
            $table->unsignedBigInteger('fabric_color_id');
            
            $table->foreign('fabric_color_id')
                ->references('id')
                ->on('fabric_colors')
                ->onDelete('cascade');
            
            $table->unsignedBigInteger('size_id');
            
            $table->foreign('size_id')
                ->references('id')
                ->on('sizes')
                ->onDelete('cascade');
            
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
        Schema::dropIfExists('cut_pieces');
    }
}
