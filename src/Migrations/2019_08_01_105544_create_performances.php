<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Langyi\Performance\Models\Performance as Pmodel;

class CreatePerformances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::connection(config('performance.connection'))->create(Pmodel::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid');
            $table->string('url');
            $table->string('uri');
            $table->unsignedInteger('ct')->default(0);
            $table->unsignedInteger('wt')->default(0);
            $table->unsignedInteger('cpu')->default(0);
            $table->unsignedInteger('mu')->default(0);
            $table->unsignedInteger('pmu')->default(0);
            $table->mediumText('content');

            $table->timestamps();     
            $table->index('uid', 'idx_uid');
            $table->index('url', 'idx_url');
            $table->index('uri', 'idx_uri');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('performance.connection'))->dropIfExists(Pmodel::getTableName());
    }
}
