<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('fabric_type_id')->nullable()->constrained('fabric_types')->onDelete('cascade');
            $table->unsignedBigInteger('fabric_color_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            
            $table->float('rate',8,2)->default(40.00);

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
        Schema::dropIfExists('rates');
    }
}
