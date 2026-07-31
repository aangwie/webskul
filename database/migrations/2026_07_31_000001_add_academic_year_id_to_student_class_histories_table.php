<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_class_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('student_class_histories', 'academic_year_id')) {
                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->after('school_class_id')
                    ->constrained('academic_years')
                    ->nullOnDelete();
            }
        });

        // Backfill: pasangkan string academic_year dengan id dari tabel academic_years
        DB::statement("
            UPDATE student_class_histories sch
            JOIN academic_years ay ON ay.year = sch.academic_year
            SET sch.academic_year_id = ay.id
            WHERE sch.academic_year_id IS NULL
              AND sch.academic_year IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('student_class_histories', function (Blueprint $table) {
            if (Schema::hasColumn('student_class_histories', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
        });
    }
};
