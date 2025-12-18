<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->longText('logo')->nullable()->change();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->longText('photo')->nullable()->change();
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->longText('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->string('logo', 255)->nullable()->change();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('photo', 255)->nullable()->change();
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->change();
        });
    }
};
