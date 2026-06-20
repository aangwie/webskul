@extends('layouts.app')

@section('title', 'Isi Survei - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .skm-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .skm-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 40px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .question-item {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eef2f7;
    }
    .question-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .question-text {
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--text);
        margin-bottom: 15px;
        line-height: 1.5;
    }
    .question-number {
        display: inline-block;
        width: 28px;
        height: 28px;
        background: var(--primary);
        color: var(--secondary);
        border-radius: 50%;
        text-align: center;
        line-height: 28px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-right: 10px;
    }
    .rating-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    .rating-option {
        position: relative;
    }
    .rating-option input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 2;
    }
    .rating-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 15px 10px;
        border: 2px solid #eef2f7;
        border-radius: 12px;
        text-align: center;
        transition: var(--transition);
        background: #fdfdfd;
    }
    .rating-option input:checked + .rating-label {
        border-color: var(--primary);
        background: rgba(30, 58, 95, 0.05);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
    }
    .rating-option input:hover + .rating-label {
        border-color: var(--primary);
    }
    .rating-score {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
    }
    .rating-desc {
        font-size: 0.75rem;
        color: var(--text-light);
        line-height: 1.3;
    }
    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 40px;
        background: var(--primary);
        color: var(--secondary);
        border: none;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        width: 100%;
        margin-top: 30px;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(30, 58, 95, 0.2);
        background: var(--primary-light);
    }
    .progress-steps {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
    }
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
    .step-inactive {
        background: #eef2f7;
        color: var(--text-light);
    }
    /* Honeypot - hidden from users */
    .honeypot-field {
        position: absolute;
        left: -9999px;
        opacity: 0;
        height: 0;
        overflow: hidden;
    }
    @media (max-width: 768px) {
        .skm-card {
            padding: 30px 20px;
        }
        .rating-options {
            grid-template-columns: repeat(2, 1fr);
        }
        .rating-label {
            padding: 12px 8px;
        }
        .step span {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<section class="section" style="background: var(--body-bg);">
    <div class="container">
        <div class="skm-container animate-fade-in">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 class="section-title">Survei Kepuasan Masyarakat</h1>
                <p class="section-subtitle">Silakan berikan penilaian Anda terhadap pelayanan {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}.</p>
            </div>

            <div class="progress-steps">
                <div class="step step-done">
                    <i class="fas fa-check"></i> <span>Data Diri</span>
                </div>
                <div class="step step-active">
                    <i class="fas fa-edit"></i> <span>Isi Survei</span>
                </div>
                <div class="step step-inactive">
                    <i class="fas fa-flag-checkered"></i> <span>Selesai</span>
                </div>
            </div>

            <div class="skm-card">
                <div style="margin-bottom: 25px; font-size: 0.9rem; color: var(--text-light);">
                    <i class="fas fa-info-circle"></i> Skala penilaian: 1 = Sangat Buruk, 2 = Buruk, 3 = Baik, 4 = Sangat Baik
                </div>

                <form action="{{ route('skm.submit-survey') }}" method="POST" id="surveyForm">
                    @csrf

                    @foreach($questions as $q)
                        <div class="question-item">
                            <div class="question-text">
                                <span class="question-number">{{ $loop->iteration }}</span>
                                {{ $q->question_text }}
                            </div>
                            <div class="rating-options">
                                @for($score = 1; $score <= 4; $score++)
                                    @php
                                        $descriptions = [
                                            1 => 'Tidak Sesuai / Sangat Buruk',
                                            2 => 'Kurang Sesuai / Buruk',
                                            3 => 'Sesuai / Baik',
                                            4 => 'Sangat Sesuai / Sangat Baik',
                                        ];
                                        $shortDesc = [
                                            1 => 'Sangat Buruk',
                                            2 => 'Buruk',
                                            3 => 'Baik',
                                            4 => 'Sangat Baik',
                                        ];
                                    @endphp
                                    <label class="rating-option">
                                        <input type="radio" name="score_{{ $q->id }}" value="{{ $score }}" required>
                                        <div class="rating-label">
                                            <div class="rating-score">{{ $score }}</div>
                                            <div class="rating-desc">{{ $shortDesc[$score] }}</div>
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    @endforeach

                    <!-- Honeypot Protection -->
                    <div class="honeypot-field">
                        <input type="text" name="honeypot" value="" tabindex="-1" autocomplete="off">
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Survei
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.getElementById('surveyForm')?.addEventListener('submit', function(e) {
        const form = this;
        const radios = form.querySelectorAll('input[type="radio"]');
        const questions = new Set();

        // Get all questions that should have answers
        radios.forEach(r => questions.add(r.name));

        let allAnswered = true;
        questions.forEach(name => {
            const checked = form.querySelector(`input[name="${name}"]:checked`);
            if (!checked) allAnswered = false;
        });

        if (!allAnswered) {
            e.preventDefault();
            alert('Silakan jawab semua pertanyaan sebelum mengirim survei.');
        }
    });
</script>
@endsection