<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->date('receiving_date');
            $table->string('mill_id');
            $table->string('mill_ref_id');
            $table->integer('width')->default(0);



            $table->foreignId('fabric_color_id')->constrained('fabric_colors')->onDelete('cascade');
            $table->foreignId('fabric_type_id')->constrained('fabric_types')->onDelete('cascade');
            $table->integer('total_quantity')->default(0);
            $table->timestamps();
        });
        
        Schema::create('fabric_rolls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('fabric_id')->constrained('fabrics')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
        


        Schema::create('fabric_roll_used_quantity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_roll_id')->nullable()->constrained('fabric_rolls')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
        

        
        Schema::create('fabric_roll_used_size_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('size_id')->nullable()->constrained('sizes')->onDelete('cascade');
            $table->foreignId('fabric_roll_id')->nullable()->constrained('fabric_rolls')->onDelete('cascade');
            
            $table->unsignedBigInteger('fabric_roll_used_quantity_log_id'); 
            $table->foreign('fabric_roll_used_quantity_log_id','fruql_id_foreign')->references('id')->on('fabric_roll_used_quantity_logs');

            $table->integer('pieces');
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
        Schema::dropIfExists('fabrics');
    }
}
