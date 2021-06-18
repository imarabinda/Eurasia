<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fileables', function (Blueprint $table) {
            
            $table->unsignedBigInteger('file_id');
            
            $table->foreign('file_id')
                ->references('id')
                ->on('files')
                ->onDelete('cascade');

            $table->uuidMorphs('fileable');

            $table->primary(['file_id'], 'file_id_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fileables');
    }
}
