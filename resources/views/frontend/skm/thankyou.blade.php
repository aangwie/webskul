@extends('layouts.app')

@section('title', 'Terima Kasih - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .thankyou-container {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
    }
    .thankyou-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 60px 40px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .success-icon {
        width: 100px;
        height: 100px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 3rem;
        color: var(--success);
    }
    .thankyou-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 15px;
    }
    .thankyou-text {
        color: var(--text-light);
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 30px;
        background: var(--primary);
        color: var(--secondary);
        text-decoration: none;
        border-radius: 14px;
        font-weight: 600;
        transition: var(--transition);
    }
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(30, 58, 95, 0.2);
        background: var(--primary-light);
        color: var(--secondary);
    }
    @media (max-width: 768px) {
        .thankyou-card {
            padding: 40px 20px;
        }
        .thankyou-title {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<section class="section" style="background: var(--body-bg);">
    <div class="container">
        <div class="thankyou-container animate-fade-in">
            <div class="progress-steps" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 30px;">
                <div class="step step-done">
                    <i class="fas fa-check"></i> <span>Data Diri</span>
                </div>
                <div class="step step-done">
                    <i class="fas fa-check"></i> <span>Isi Survei</span>
                </div>
                <div class="step step-active">
                    <i class="fas fa-flag-checkered"></i> <span>Selesai</span>
                </div>
            </div>

            <div class="thankyou-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="thankyou-title">Terima Kasih!</h1>
                <p class="thankyou-text">
                    Terima kasih telah berpartisipasi dalam Survei Kepuasan Masyarakat (SKM) di {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}.<br><br>
                    Partisipasi Anda sangat berarti bagi kami untuk terus meningkatkan kualitas pelayanan.
                </p>
                <a href="{{ route('home') }}" class="btn-back">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .step-active {
        background: var(--primary);
        color: var(--secondary);
    }
    .step-done {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }
    @media (max-width: 768px) {
        .step span {
            display: none;
        }
    }
</style>