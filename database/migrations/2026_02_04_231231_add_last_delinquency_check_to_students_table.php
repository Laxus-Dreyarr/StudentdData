<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_last_delinquency_check_to_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->timestamp('last_delinquency_check')->nullable()->after('sy');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('last_delinquency_check');
        });
    }
};