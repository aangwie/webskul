<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('host_url');
            $table->string('api_key');
            $table->string('nomor_pengirim');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_api_settings');
    }
};