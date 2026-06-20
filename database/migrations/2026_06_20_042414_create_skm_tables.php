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
        // Questions table
        Schema::create('skm_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_text');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Respondents table
        Schema::create('skm_respondents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->year('year');
            $table->timestamps();
        });

        // Survey responses table
        Schema::create('skm_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('respondent_id')->constrained('skm_respondents')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('skm_questions')->onDelete('cascade');
            $table->tinyInteger('score')->comment('1-4 scale');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skm_responses');
        Schema::dropIfExists('skm_respondents');
        Schema::dropIfExists('skm_questions');
    }
};