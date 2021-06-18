<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFabricType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fabric_types', function (Blueprint $table) {
            $table->unsignedBigInteger('product_type_id');
             $table->unsignedBigInteger('fabric_type_id');
                  
            $table->foreign('product_type_id')
                ->references('id')
                ->on('product_types')
                ->onDelete('cascade');
                
            $table->foreign('fabric_type_id')
                ->references('id')
                ->on('fabric_types')
                ->onDelete('cascade');
            
            // $table->primary(['fabric_type_id', 'product_type_id']);
        });
            
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_fabric_types');
    }
}
