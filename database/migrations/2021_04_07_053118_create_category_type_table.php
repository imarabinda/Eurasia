<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_category_types', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id');
             $table->unsignedBigInteger('product_type_id');
                  
            $table->foreign('product_type_id')
                ->references('id')
                ->on('product_types')
                ->onDelete('cascade');
                
            $table->foreign('product_category_id')
                ->references('id')
                ->on('product_categories')
                ->onDelete('cascade');
                
            // $table->primary(['product_category_id', 'product_type_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_category_types');
    }
}
