@extends('layouts.app')

@section('title', 'Kegiatan - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

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

        .activities-content {
            max-width: 1200px;
            margin: -40px auto 0;
            padding: 0 20px 80px;
            position: relative;
            z-index: 10;
        }

        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .activity-card {
            background: var(--secondary);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            text-decoration: none;
            display: block;
        }

        .activity-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .activity-image {
            height: 220px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .activity-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .activity-image i {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .activity-category {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 6px 15px;
            background: var(--secondary);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .activity-content {
            padding: 25px;
        }

        .activity-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .activity-excerpt {
            color: var(--text-light);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .activity-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-light);
            font-size: 0.85rem;
        }

        .activity-meta i {
            color: var(--primary);
        }

        .pagination-wrapper {
            margin-top: 50px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 10px;
            list-style: none;
        }

        .pagination li a,
        .pagination li span {
            display: block;
            padding: 10px 18px;
            background: var(--secondary);
            color: var(--text);
            text-decoration: none;
            border-radius: 10px;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .pagination li.active span {
            background: var(--primary);
            color: var(--secondary);
        }

        .pagination li a:hover {
            background: var(--primary);
            color: var(--secondary);
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

            .activities-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Kegiatan & Berita</h1>
        <p>Informasi terbaru mengenai kegiatan dan berita sekolah</p>
    </div>

    <div class="activities-content">
        @if($activities->isEmpty())
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <p>Belum ada kegiatan atau berita.</p>
            </div>
        @else
            <div class="activities-grid">
                @foreach($activities as $activity)
                    <a href="{{ route('activities.show', $activity->slug) }}" class="activity-card">
                        <div class="activity-image">
                            @if($activity->image)
                                @if(Str::startsWith($activity->image, 'data:'))
                                    <img src="{{ $activity->image }}" alt="{{ $activity->title }}">
                                @else
                                    <img src="{{ route('public.storage.view', ['path' => $activity->image]) }}"
                                        alt="{{ $activity->title }}">
                                @endif
                            @else
                                <i class="fas fa-newspaper"></i>
                            @endif
                            <span class="activity-category">{{ $activity->category == 'news' ? 'Berita' : 'Acara' }}</span>
                        </div>
                        <div class="activity-content">
                            <h3 class="activity-title">{{ $activity->title }}</h3>
                            <p class="activity-excerpt">{{ Str::limit(strip_tags($activity->content), 120) }}</p>
                            <div class="activity-meta">
                                <span><i class="far fa-calendar-alt"></i>
                                    {{ $activity->published_at ? $activity->published_at->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
@endsection