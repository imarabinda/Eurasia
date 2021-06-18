<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workables', function (Blueprint $table) {
            
            $table->string('issued_quantity');
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('cascade');

            $table->unsignedBigInteger('embroidery_stock_id')->nullable();
            $table->foreign('embroidery_stock_id')
            ->references('id')
            ->on('embroidery_stocks')
            ->onDelete('cascade');
            
            $table->uuidMorphs('workable');
            
            
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
        Schema::dropIfExists('workables');
    }
}
