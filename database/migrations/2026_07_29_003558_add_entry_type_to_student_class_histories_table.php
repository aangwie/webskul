<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_class_histories', function (Blueprint $table) {
            $table->string('entry_type')->nullable()->after('action')->comment('new or transfer, only for registered action');
        });
    }

    public function down(): void
    {
        Schema::table('student_class_histories', function (Blueprint $table) {
            $table->dropColumn('entry_type');
        });
    }
};