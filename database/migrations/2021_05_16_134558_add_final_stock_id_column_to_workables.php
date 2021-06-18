<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalStockIdColumnToWorkables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workables', function (Blueprint $table) {
            $table->unsignedBigInteger('final_stock_id')->nullable();
            
            $table->foreign('final_stock_id')
            ->references('id')
            ->on('final_stocks')
            ->onDelete('cascade');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workables', function (Blueprint $table) {
            $table->dropForeign(['final_stock_id']);
            $table->dropColumn('final_stock_id');
        });
    }
}
