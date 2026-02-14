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
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
                $table->string('name');
                $table->enum('gender', ['male', 'female']); // Laki-laki/Perempuan
                $table->string('nis')->nullable();          // Nomor Induk Siswa
                $table->year('enrollment_year');            // Tahun masuk
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
