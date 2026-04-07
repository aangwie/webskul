@extends('layouts.app')

@section('title', 'Informasi - ' . ($school->name ?? 'SMP Negeri 6 Sudimoro'))

@section('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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

        .info-content {
            max-width: 900px;
            margin: -40px auto 0;
            padding: 0 20px 80px;
            position: relative;
            z-index: 10;
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background: var(--secondary);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 30px;
            transition: var(--transition);
            border-left: 4px solid var(--primary);
        }

        .info-card:hover {
            transform: translateX(10px);
            box-shadow: var(--shadow-lg);
        }

        .info-card.important {
            border-left-color: var(--accent-gold);
            background: linear-gradient(135deg, #fffdf5 0%, var(--secondary) 100%);
        }

        .info-card.important::before {
            content: 'PENTING';
            display: inline-block;
            background: var(--accent-gold);
            color: var(--primary-dark);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .info-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 5px;
            /* Reduced from 15px */
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .info-title i {
            color: var(--primary);
        }

        .info-text {
            color: var(--text-light);
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .info-text .ql-editor {
            padding: 0 !important;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
            color: inherit;
            overflow-y: visible;
            min-height: auto !important;
        }

        .info-text .ql-editor > *:first-child {
            margin-top: 0;
        }

        .info-text .ql-editor > *:last-child {
            margin-bottom: 0;
        }

        .info-text .ql-editor p {
            margin-bottom: 0.25em;
        }
        
        /* Collapse empty paragraphs that users might have left in the WYSIWYG editor */
        .info-text .ql-editor p:empty::before,
        .info-text .ql-editor p:has(br:only-child) {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .info-date {
            margin-top: 5px;
            /* Reduced from 15px */
            padding-top: 5px;
            /* Reduced from 15px */
            border-top: 1px solid var(--accent);
            color: var(--text-light);
            font-size: 0.85rem;
        }

        .info-date i {
            margin-right: 5px;
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

            .info-card {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Informasi & Pengumuman</h1>
        <p>Informasi penting untuk siswa dan orang tua</p>
    </div>

    <div class="info-content">
        @if($informations->isEmpty())
            <div class="empty-state">
                <i class="fas fa-bullhorn"></i>
                <p>Belum ada informasi atau pengumuman.</p>
            </div>
        @else
            <div class="info-list">
                @foreach($informations as $info)
                    <div class="info-card {{ $info->is_important ? 'important' : '' }}">
                        <h3 class="info-title">
                            <i class="fas fa-info-circle"></i>
                            {{ $info->title }}
                        </h3>
                        <div class="info-text ql-snow">
                            <div class="ql-editor">
                                {!! $info->content !!}
                            </div>
                        </div>
                        <div class="info-date">
                            <i class="far fa-clock"></i>
                            {{ $info->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $informations->links() }}
            </div>
        @endif
    </div>
@endsection