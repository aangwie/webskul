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
        if (!Schema::hasTable('pmb_registrations')) {
            Schema::create('pmb_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('nisn');
                $table->string('nik');
                $table->string('birth_place');
                $table->date('birth_date');
                $table->text('address');
                $table->enum('registration_type', ['baru', 'pindahan']);
                $table->string('mother_name');
                $table->string('father_name');
                $table->string('guardian_name')->nullable();
                $table->string('phone_number');
                $table->longText('kk_attachment');
                $table->longText('birth_certificate_attachment');
                $table->longText('ijazah_attachment');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_registrations');
    }
};
