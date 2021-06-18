<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStitchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stitches', function (Blueprint $table) {
            $table->id();
            
            $table->date('issue_date');
            $table->string('vendor_name');
            $table->string('vendor_gst_no')->nullable();
            $table->string('vendor_address')->nullable();
            $table->string('consignor_name');
            $table->string('consignor_address')->nullable();
            $table->string('consignor_gst_no')->nullable();
            $table->string('job_work_type')->default('Stitching');
            $table->string('challan_number',$precision=0);
            
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
        Schema::dropIfExists('stitches');
    }
}
