<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('committee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('committee_payments', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete()->after('committee_fee_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('committee_payments', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};