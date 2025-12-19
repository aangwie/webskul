@extends('layouts.app')

@section('title', $activity->title . ' - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: var(--secondary);
        padding: 100px 20px 60px;
        text-align: center;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 15px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .page-header-meta {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .page-header-meta span {
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .page-header-meta i {
        margin-right: 5px;
    }

    .article-content {
        max-width: 900px;
        margin: -40px auto 0;
        padding: 0 20px 80px;
        position: relative;
        z-index: 10;
    }

    .article-card {
        background: var(--secondary);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .article-image {
        width: 100%;
        height: 400px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .article-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .article-image i {
        font-size: 6rem;
        color: rgba(255, 255, 255, 0.3);
    }

    .article-body {
        padding: 50px;
    }

    .article-body p {
        color: var(--text);
        line-height: 1.9;
        font-size: 1.05rem;
        margin-bottom: 20px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        margin-top: 30px;
        transition: var(--transition);
    }

    .back-link:hover {
        color: var(--primary-light);
        transform: translateX(-5px);
    }

    .related-section {
        margin-top: 60px;
    }

    .related-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 30px;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .related-card {
        background: var(--secondary);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        text-decoration: none;
        transition: var(--transition);
    }

    .related-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .related-image {
        height: 150px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .related-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .related-image i {
        font-size: 2.5rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .related-info {
        padding: 20px;
    }

    .related-info h4 {
        color: var(--text);
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .related-info span {
        color: var(--text-light);
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 1.5rem;
        }

        .article-image {
            height: 250px;
        }

        .article-body {
            padding: 30px 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <span class="activity-category" style="display: inline-block; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.8rem; margin-bottom: 15px;">
        {{ $activity->category == 'news' ? 'Berita' : 'Acara' }}
    </span>
    <h1>{{ $activity->title }}</h1>
    <div class="page-header-meta">
        <span><i class="far fa-calendar-alt"></i> {{ $activity->published_at ? $activity->published_at->format('d M Y') : '-' }}</span>
    </div>
</div>

<div class="article-content">
    <div class="article-card">
        @if($activity->image)
        <div class="article-image">
            @if(Str::startsWith($activity->image, 'data:'))
            <img src="{{ $activity->image }}" alt="{{ $activity->title }}">
            @else
            <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->title }}">
            @endif
        </div>
        @endif

        <div class="article-body">
            {!! nl2br(e($activity->content)) !!}

            <a href="{{ route('activities.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Kegiatan
            </a>
        </div>
    </div>

    @if($relatedActivities->isNotEmpty())
    <div class="related-section">
        <h3 class="related-title">Kegiatan Terkait</h3>
        <div class="related-grid">
            @foreach($relatedActivities as $related)
            <a href="{{ route('activities.show', $related->slug) }}" class="related-card">
                <div class="related-image">
                    @if($related->image)
                    @if(Str::startsWith($activity->image, 'data:'))
                    <img src="{{ $activity->image }}" alt="{{ $activity->title }}">
                    @else
                    <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->title }}">
                    @endif
                    @else
                    <i class="fas fa-newspaper"></i>
                    @endif
                </div>
                <div class="related-info">
                    <h4>{{ $related->title }}</h4>
                    <span><i class="far fa-calendar-alt"></i> {{ $related->published_at ? $related->published_at->format('d M Y') : '-' }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection