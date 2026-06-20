@extends('admin.layouts.app')

@section('title', 'Pertanyaan SKM')
@section('page-title', 'Pertanyaan SKM (Survei Kepuasan Masyarakat)')

@section('styles')
<style>
    .question-list {
        max-width: 800px;
    }
    .question-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 20px;
        background: var(--secondary);
        border: 1px solid var(--accent);
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: grab;
    }
    .question-item:active {
        cursor: grabbing;
    }
    .question-item .drag-handle {
        color: var(--text-light);
        cursor: grab;
        font-size: 1.2rem;
    }
    .question-item .question-number {
        font-weight: 700;
        color: var(--primary);
        min-width: 30px;
    }
    .question-item .question-text {
        flex: 1;
        font-size: 0.95rem;
    }
    .question-item .question-actions {
        display: flex;
        gap: 8px;
    }
    .badge-active {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-inactive {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .question-item.dragging {
        opacity: 0.5;
        border-style: dashed;
    }
    .question-item.drag-over {
        border-color: var(--primary);
        border-style: dashed;
        background: rgba(30, 58, 95, 0.05);
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-plus-circle"></i> Tambah Pertanyaan Baru</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.skm.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Pertanyaan</label>
                <textarea name="question_text" class="form-textarea" placeholder="Masukkan pertanyaan SKM..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pertanyaan
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Daftar Pertanyaan SKM</h2>
        <span class="badge badge-success">{{ $questions->count() }} Pertanyaan</span>
    </div>
    <div class="card-body">
        @if($questions->isEmpty())
            <p style="text-align: center; color: var(--text-light); padding: 40px;">
                <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
                Belum ada pertanyaan. Silakan tambah pertanyaan baru.
            </p>
        @else
            <div class="question-list" id="question-list">
                @foreach($questions as $index => $question)
                    <div class="question-item" data-id="{{ $question->id }}">
                        <div class="drag-handle">
                            <i class="fas fa-grip-lines"></i>
                        </div>
                        <span class="question-number">{{ $loop->iteration }}.</span>
                        <div class="question-text">{{ $question->question_text }}</div>
                        <span class="{{ $question->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <div class="question-actions">
                            <button type="button" class="btn btn-sm btn-warning" onclick="editQuestion({{ $question->id }}, '{{ addslashes($question->question_text) }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="{{ route('admin.skm.toggle-active', $question) }}" class="btn btn-sm {{ $question->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="fas {{ $question->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </a>
                            <form action="{{ route('admin.skm.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <p style="margin-top: 15px; font-size: 0.85rem; color: var(--text-light);">
                <i class="fas fa-info-circle"></i> Seret dan lepas untuk mengurutkan pertanyaan.
            </p>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div style="background: var(--secondary); border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; box-shadow: var(--shadow-lg);">
        <h3 style="margin-bottom: 20px;">Edit Pertanyaan</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Pertanyaan</label>
                <textarea name="question_text" id="editQuestionText" class="form-textarea" required></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const questionList = document.getElementById('question-list');

    if (questionList) {
        new Sortable(questionList, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'dragging',
            dragClass: 'drag-over',
            onEnd: function() {
                const items = questionList.querySelectorAll('.question-item');
                const orders = [];
                items.forEach(item => {
                    orders.push(item.dataset.id);
                });

                fetch('{{ route("admin.skm.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orders: orders })
                });
            }
        });
    }

    function editQuestion(id, text) {
        const modal = document.getElementById('editModal');
        modal.style.display = 'flex';
        document.getElementById('editQuestionText').value = text;
        document.getElementById('editForm').action = '/admin/skm/' + id;
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal on overlay click
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
@endsection