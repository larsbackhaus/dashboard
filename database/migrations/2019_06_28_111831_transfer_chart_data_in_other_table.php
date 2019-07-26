<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransferChartDataInOtherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_uploads_data', function (Blueprint $table) {
            $table->dropColumn('ch_left');
            $table->dropColumn('ch_top');
            $table->dropColumn('ch_width');
            $table->dropColumn('ch_height');
        });

        Schema::create('chart', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('data_id');
            $table->integer('ch_left')->default(50);
            $table->integer('ch_top')->default(50);
            $table->integer('ch_width')->default(500);
            $table->integer('ch_height')->default(400);
            $table->boolean('visible')->default(false);
            $table->string('mode')->default('diagram');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
