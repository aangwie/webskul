<div style="display: flex; gap: 5px; flex-wrap: wrap;">
    @if(!$borrowing->is_returned)
        <button class="btn btn-success btn-sm" onclick='showReturnModal(@json($borrowing))' title="Tandai Kembali">
            <i class="fas fa-check"></i>
        </button>
    @endif
    <button class="btn btn-warning btn-sm" onclick='showEditModal(@json($borrowing))'>
        <i class="fas fa-edit"></i>
    </button>
    <form action="{{ route('admin.library.borrowings.destroy', $borrowing) }}" method="POST"
        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>