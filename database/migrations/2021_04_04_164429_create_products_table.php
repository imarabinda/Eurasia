<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->longText('image');
            $table->timestamps();
        });

        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->timestamps();
        });

        Schema::create('fabric_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->timestamps();
        });

        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->integer('height')->nullable(false);
            $table->integer('width')->nullable(false);
            $table->timestamps();
        });

        Schema::create('fabric_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->timestamps();
        });


        
         Schema::create('fabric_type_colors', function (Blueprint $table) {
             $table->unsignedBigInteger('fabric_type_id');
             $table->unsignedBigInteger('fabric_color_id');
                  
            $table->foreign('fabric_type_id')
                ->references('id')
                ->on('fabric_types')
                ->onDelete('cascade');

                
            $table->foreign('fabric_color_id')
                ->references('id')
                ->on('fabric_colors')
                ->onDelete('cascade');

            $table->primary(['fabric_color_id', 'fabric_type_id'], 'fabric_type_colors_fabric_color_id_fabric_type_id_primary');
        });
        

       
        Schema::create('welted_edges_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->timestamps();
        });
        
        Schema::create('thread_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable(false);
            $table->text('description');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->onDelete('cascade');
            $table->foreignId('fabric_type_id')->nullable()->constrained('fabric_types')->onDelete('cascade');
            $table->foreignId('fabric_color_id')->nullable()->constrained('fabric_colors')->onDelete('cascade');
            $table->foreignId('size_id')->nullable()->constrained('sizes')->onDelete('cascade');
            $table->foreignId('welted_edges_color_id')->nullable()->constrained('welted_edges_colors')->onDelete('cascade');
            $table->integer('number_of_stitches')->default(0);
            $table->float('rate',8,2)->default(0.00);
            $table->timestamps();
        });


         Schema::create('product_thread_colors', function (Blueprint $table) {
             $table->unsignedBigInteger('product_id');
             $table->unsignedBigInteger('thread_color_id');

            $table->foreign('thread_color_id')
                ->references('id')
                ->on('thread_colors')
                ->onDelete('cascade');
                
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->primary(['thread_color_id', 'product_id'], 'product_thread_colors_thread_color_id_product_id_primary');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('product_categories');
        
        Schema::dropIfExists('fabric_types');
        Schema::dropIfExists('fabric_colors');
        Schema::dropIfExists('sizes');

        Schema::dropIfExists('welted_edges_colors');
        Schema::dropIfExists('thread_colors');
        
    }
}
