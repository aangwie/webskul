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
        Schema::table('committee_activities', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->after('name')->default(0);
            $table->integer('quantity')->after('unit_price')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_activities', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'quantity']);
        });
    }
};
