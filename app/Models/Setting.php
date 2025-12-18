<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all SMTP settings
     */
    public static function getSmtpSettings()
    {
        return [
            'mail_mailer' => static::get('mail_mailer', 'smtp'),
            'mail_host' => static::get('mail_host', ''),
            'mail_port' => static::get('mail_port', '587'),
            'mail_username' => static::get('mail_username', ''),
            'mail_password' => static::get('mail_password', ''),
            'mail_encryption' => static::get('mail_encryption', 'tls'),
            'mail_from_address' => static::get('mail_from_address', ''),
            'mail_from_name' => static::get('mail_from_name', 'SMP Negeri 6 Sudimoro'),
        ];
    }

    /**
     * Set SMTP settings
     */
    public static function setSmtpSettings($data)
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }
}
