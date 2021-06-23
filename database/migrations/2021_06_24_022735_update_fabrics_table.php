<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFabricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabrics', function(Blueprint $table){
            $table->float('width',8,2)->change();
            $table->float('total_quantity',8,2)->default(0.00)->change();
        });
        
        Schema::table('fabric_rolls', function(Blueprint $table){
            $table->float('quantity',8,2)->default(0.00)->change();
        });
        Schema::table('fabric_roll_used_quantity_logs', function(Blueprint $table){
            $table->float('quantity',8,2)->default(0.00)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('fabrics', function(Blueprint $table){
            $table->integer('width')->change();
            $table->integer('total_quantity')->change();
        });
        
        Schema::table('fabric_rolls', function(Blueprint $table){
            $table->integer('quantity')->change();
        });
        Schema::table('fabric_roll_used_quantity_logs', function(Blueprint $table){
            $table->integer('quantity')->change();
        });
        
    }
}
