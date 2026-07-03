<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappApiSetting extends Model
{
    protected $table = 'whatsapp_api_settings';

    protected $fillable = [
        'host_url',
        'api_key',
        'nomor_pengirim',
    ];
}