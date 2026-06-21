<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('school_profiles', 'maklumat_pelayanan_image')) {
            Schema::table('school_profiles', function (Blueprint $table) {
                $table->longText('maklumat_pelayanan_image')->nullable()->after('logo_ssn');
            });
        }
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn('maklumat_pelayanan_image');
        });
    }
};