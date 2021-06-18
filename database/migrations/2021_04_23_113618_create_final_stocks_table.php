<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_stocks', function (Blueprint $table) {
            $table->id();
            


            $table->unsignedBigInteger('product_id');
            
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->unsignedBigInteger('stitching_id');
            
            $table->foreign('stitching_id')
                ->references('id')
                ->on('stitches')
                ->onDelete('cascade');

            $table->integer('received_stitches')->default(0);
            $table->integer('received_damage')->default(0);
            
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
        Schema::dropIfExists('embroidery_stocks');
    }
}
