<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmbroideryStockLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('embroidery_stock_logs', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('production_id');
            
            $table->foreign('production_id')
                ->references('id')
                ->on('productions')
                ->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->integer('received_embroidery')->default(0);
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
        Schema::dropIfExists('embroidery_stock_logs');
    }
}
