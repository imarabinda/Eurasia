<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromStitchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stitches', function (Blueprint $table) {
            $table->dropColumn('vendor_name');
            $table->dropColumn('vendor_address');
            $table->dropColumn('vendor_gst_no');

            $table->foreignId('tailor_id')->nullable()->constrained('tailors')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stitches', function (Blueprint $table) {
            $table->string('vendor_name');
            $table->string('vendor_gst_no')->nullable();
            $table->string('vendor_address')->nullable();
            
            $table->dropForeign(['tailor_id']);
            $table->dropColumn('tailor_id');
        
        });
    }
}
