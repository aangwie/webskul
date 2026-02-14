<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('judul_buku');
            $table->string('penerbit');
            $table->string('pengarang');
            $table->year('tahun_perolehan');
            $table->string('asal_usul');
            $table->foreignId('book_type_id')->constrained('book_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
