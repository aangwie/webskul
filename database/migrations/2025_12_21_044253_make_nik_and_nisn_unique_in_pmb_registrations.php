<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $existsNik = count(DB::select("SHOW INDEXES FROM pmb_registrations WHERE Key_name = 'pmb_registrations_nik_unique'")) > 0;
        if (!$existsNik) {
            Schema::table('pmb_registrations', function (Blueprint $table) {
                $table->unique('nik');
            });
        }

        $existsNisn = count(DB::select("SHOW INDEXES FROM pmb_registrations WHERE Key_name = 'pmb_registrations_nisn_unique'")) > 0;
        if (!$existsNisn) {
            Schema::table('pmb_registrations', function (Blueprint $table) {
                $table->unique('nisn');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmb_registrations', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropUnique(['nisn']);
        });
    }
};
