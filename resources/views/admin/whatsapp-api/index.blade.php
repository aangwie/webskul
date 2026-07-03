@extends('admin.layouts.app')

@section('title', 'WhatsApp API')
@section('page-title', 'WhatsApp API')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-cog"></i> Pengaturan WhatsApp API</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.whatsapp-api.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Host / URL</label>
                <input type="url" name="host_url" class="form-input"
                    value="{{ old('host_url', $setting->host_url ?? '') }}"
                    placeholder="https://wa.billnesia.com/send-message" required>
                @error('host_url') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">API Key</label>
                <input type="text" name="api_key" class="form-input"
                    value="{{ old('api_key', $setting->api_key ?? '') }}"
                    placeholder="YOUR_API_KEY" required>
                @error('api_key') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Pengirim</label>
                <input type="text" name="nomor_pengirim" class="form-input"
                    value="{{ old('nomor_pengirim', $setting->nomor_pengirim ?? '') }}"
                    placeholder="628123456789" required>
                @error('nomor_pengirim') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-paper-plane"></i> Test Kirim Pesan</h2>
    </div>
    <div class="card-body">
        @if(session('test_result'))
            <div class="alert alert-success">
                <strong>HTTP Code:</strong> {{ session('test_http_code') }}<br>
                <strong>Response:</strong>
                <pre style="margin-top: 8px; background: var(--accent); padding: 12px; border-radius: 8px; overflow-x: auto;">{{ session('test_result') }}</pre>
            </div>
        @endif

        <form action="{{ route('admin.whatsapp-api.test') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Nomor Penerima</label>
                <input type="text" name="nomor_penerima" class="form-input"
                    placeholder="628987654321" required>
                @error('nomor_penerima') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Pesan</label>
                <textarea name="pesan" class="form-textarea"
                    placeholder="Halo dari WhatsAppKu!" required></textarea>
                @error('pesan') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane"></i> Kirim Test
            </button>
        </form>
    </div>
</div>

@if($setting)
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-code"></i> Contoh Penggunaan</h2>
    </div>
    <div class="card-body">
        <p style="margin-bottom: 12px; color: var(--text-light);"><strong>cURL:</strong></p>
        <pre style="background: var(--accent); padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 0.85rem; line-height: 1.6;">curl -X POST {{ $setting->host_url }} \
      -H "Content-Type: application/x-www-form-urlencoded" \
      -d "nomor_pengirim={{ $setting->nomor_pengirim }}" \
      -d "api_key={{ substr($setting->api_key, 0, 4) }}..." \
      -d "nomor_penerima=628987654321" \
      -d "pesan=Halo dari WhatsAppKu!"</pre>

        <p style="margin: 16px 0 12px; color: var(--text-light);"><strong>PHP:</strong></p>
        <pre style="background: var(--accent); padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 0.85rem; line-height: 1.6;">{{ '<?php' }}
$data = [
    'nomor_pengirim' => '{{ $setting->nomor_pengirim }}',
    'api_key' => '{{ substr($setting->api_key, 0, 4) }}...',
    'nomor_penerima' => '628987654321',
    'pesan' => 'Halo dari WhatsAppKu!',
];

$ch = curl_init('{{ $setting->host_url }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;</pre>
    </div>
</div>
@endif
@endsection