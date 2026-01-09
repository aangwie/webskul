<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('committee_expenditures', 'committee_activity_id')) {
            Schema::table('committee_expenditures', function (Blueprint $table) {
                $table->foreignId('committee_activity_id')->nullable()->after('id')->constrained('committee_activities')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_expenditures', function (Blueprint $table) {
            $table->dropForeign(['committee_activity_id']);
            $table->dropColumn('committee_activity_id');
        });
    }
};
