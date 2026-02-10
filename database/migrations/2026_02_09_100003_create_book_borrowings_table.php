<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('book_borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->enum('borrower_type', ['student', 'teacher'])->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('peminjam'); // Name of the borrower
            $table->string('identitas_peminjam')->nullable(); // NIS or NIP
            $table->string('kelas_peminjam')->nullable(); // For students
            $table->date('tanggal_pinjam');
            $table->integer('jumlah_pinjam')->default(1);
            $table->date('tanggal_kembali')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_borrowings');
    }
};
