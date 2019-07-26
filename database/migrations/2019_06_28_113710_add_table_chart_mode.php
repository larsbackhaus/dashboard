<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableChartMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_mode', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name');
        });

        DB::table('chart_mode')->insert(['name' => 'Diagram']);
        DB::table('chart_mode')->insert(['name' => 'Linear']);
        DB::table('chart_mode')->insert(['name' => 'Radian']);

        Schema::table('chart', function (Blueprint $table) {
            $table->dropColumn('mode');
        });

        Schema::table('chart', function (Blueprint $table) {
            $table->smallInteger('mode')->default(1);
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
