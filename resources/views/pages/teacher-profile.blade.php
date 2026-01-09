@extends('layouts.app')

@section('title', 'Profil Guru - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--secondary);
            padding: 100px 20px 60px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .page-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .teachers-content {
            max-width: 1200px;
            margin: -40px auto 0;
            padding: 0 20px 80px;
            position: relative;
            z-index: 10;
        }

        .teachers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .teacher-card {
            background: var(--secondary);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            text-align: center;
        }

        .teacher-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .teacher-photo {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .teacher-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .teacher-photo i {
            font-size: 5rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .teacher-info {
            padding: 25px;
        }

        .teacher-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 5px;
        }

        .teacher-position {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .teacher-education {
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .teacher-nip {
            color: var(--text-light);
            font-size: 0.8rem;
            background: var(--accent);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: var(--secondary);
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.8rem;
            }

            .teachers-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Profil Guru</h1>
        <p>Tenaga pendidik profesional {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</p>
    </div>

    <div class="teachers-content">
        @if($teachers->isEmpty())
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>Data guru belum tersedia.</p>
            </div>
        @else
            <div class="teachers-grid">
                @foreach($teachers as $teacher)
                    <div class="teacher-card">
                        <div class="teacher-photo">
                            @if($teacher->photo)
                                @if(Str::startsWith($teacher->photo, 'data:'))
                                    <img src="{{ $teacher->photo }}" alt="{{ $teacher->name }}">
                                @else
                                    <img src="{{ route('public.storage.view', ['path' => $teacher->photo]) }}" alt="{{ $teacher->name }}">
                                @endif
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        <div class="teacher-info">
                            <h3 class="teacher-name">{{ $teacher->name }}</h3>
                            <p class="teacher-position">{{ $teacher->position ?? 'Guru' }}</p>
                            @if($teacher->education)
                                <p class="teacher-education"><i class="fas fa-graduation-cap"></i> {{ $teacher->education }}</p>
                            @endif
                            @if($teacher->nip)
                                <span class="teacher-nip">NIP:
                                    @php
                                        $nip = $teacher->nip;
                                        $length = strlen($nip);
                                        $masked = substr($nip, 0, 2);
                                        if ($length > 2) {
                                            $maskLength = min($length, 16) - 2;
                                            $masked .= str_repeat('*', $maskLength);
                                        }
                                        if ($length > 16) {
                                            $masked .= substr($nip, 16);
                                        }
                                        echo $masked;
                                    @endphp
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection