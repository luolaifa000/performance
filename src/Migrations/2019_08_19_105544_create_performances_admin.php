<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Langyi\Performance\Models\PerformanceAdmin;

class CreatePerformancesAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::connection(config('performance.connection'))->create(PerformanceAdmin::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('admin');
            $table->string('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('performance.connection'))->dropIfExists(PerformanceAdmin::getTableName());
    }
}
