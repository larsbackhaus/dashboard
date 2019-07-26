<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExcelUserFilesTableAddChartSizesAndPosColumns2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_uploads_data', function (Blueprint $table) {
            $table->string('file_name')->after('file')->nullable(false);
            $table->integer('ch_left')->after('file_name')->default(50);
            $table->integer('ch_top')->after('ch_left')->default(50);
            $table->integer('ch_width')->after('ch_top')->default(500);
            $table->integer('ch_height')->after('ch_width')->default(400);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
