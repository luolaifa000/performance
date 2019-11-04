<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Langyi\Performance\Models\Performance as Pmdoel;


class AddSqlToPerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::connection(config('performance.connection'))->table(Pmdoel::getTableName(), function (Blueprint $table) {
            if (!Schema::hasColumn(Pmdoel::getTableName(), 'sql')) {
                $table->mediumText('sql');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('performance.connection'))->table(Pmdoel::getTableName(), function (Blueprint $table) {
            if (Schema::hasColumn(Pmdoel::getTableName(), "sql")) {
                $table->dropColumn("sql");
            }
        });
    }
}
