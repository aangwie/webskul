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
        if (!Schema::hasTable('committee_expenditures')) {
            Schema::create('committee_expenditures', function (Blueprint $table) {
                $table->id();
                $table->string('expenditure_number')->unique();
                $table->date('date');
                $table->text('description');
                $table->decimal('amount', 15, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_expenditures');
    }
};
