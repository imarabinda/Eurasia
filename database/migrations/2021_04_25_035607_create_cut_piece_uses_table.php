<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCutPieceUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cut_piece_useables', function (Blueprint $table) {
            $table->id();
            $table->integer('used_pieces');

            $table->unsignedBigInteger('cut_piece_id');
            
            $table->foreign('cut_piece_id')
                ->references('id')
                ->on('cut_pieces')
                ->onDelete('cascade');


            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->uuidMorphs('cut_piece_useable','cut_piece_useable_key');
            
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
        Schema::dropIfExists('cut_piece_useables');
    }
}
