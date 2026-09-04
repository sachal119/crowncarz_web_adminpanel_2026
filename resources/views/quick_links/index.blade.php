@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">📌 Manage Quick Links</h2>
        <!--<button class="btn btn-gradient shadow-sm px-4 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#createModal">-->
        <!--    ➕ Add Quick Link-->
        <!--</button>-->
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    
    <div class="card shadow-lg border-0 rounded-3">
    <div class="card-body p-4">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold">Title</th>
                    <!--<th class="fw-semibold">URL</th>-->
                    <th class="fw-semibold">Description</th>
                    <th class="fw-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $id => $link)
                    <tr>
                        <!-- Title -->
                        <td class="fw-bold text-dark">{{ $link['title'] ?? '' }}</td>

                        <!-- URL -->
                        <!--<td>-->
                        <!--    @if(!empty($link['url']))-->
                        <!--        <a href="{{ $link['url'] }}" target="_blank" class="text-decoration-none text-primary fw-semibold">-->
                        <!--            {{ $link['url'] }}-->
                        <!--        </a>-->
                        <!--    @else-->
                        <!--        <span class="text-muted">—</span>-->
                        <!--    @endif-->
                        <!--</td>-->

                        <!-- Description Preview -->
                        <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {!! \Illuminate\Support\Str::limit(strip_tags($link['description']), 80, '...') !!}
                            <br>
                            
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-outline-warning me-2 rounded-pill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $id }}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-primary me-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#descModal{{ $id }}">
                               View
                            </button>
                        </td>
                    </tr>

                    <!-- Full Description Modal -->
                    <div class="modal fade" id="descModal{{ $id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">📄 Full Description</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    {!! $link['description'] !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <form action="{{ route('quick-links.update', $id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header bg-gradient text-white rounded-top-4">
                                        <h5 class="modal-title">✏️ Edit Quick Link</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Title</label>
                                            <input type="text" name="title" value="{{ $link['title'] }}" class="form-control shadow-sm" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">URL</label>
                                            <input type="url" name="url" value="{{ $link['url'] ?? '' }}" class="form-control shadow-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Description</label>
                                            <textarea name="description" id="editor{{ $id }}" class="form-control shadow-sm" rows="4">{{ $link['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">❌ Cancel</button>
                                        <button type="submit" class="btn btn-gradient rounded-pill px-4">💾 Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            🚫 No quick links found. Click <strong>“Add Quick Link”</strong> to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


    <!--<div class="card shadow-lg border-0 rounded-3">-->
    <!--    <div class="card-body p-4">-->
    <!--        <table class="table align-middle table-hover">-->
    <!--            <thead class="table-light">-->
    <!--                <tr>-->
    <!--                    <th class="fw-semibold">Title</th>-->
    <!--                    <th class="fw-semibold">URL</th>-->
    <!--                    <th class="fw-semibold">Description</th>-->
    <!--                    <th class="fw-semibold text-center">Actions</th>-->
    <!--                </tr>-->
    <!--            </thead>-->
    <!--            <tbody>-->
    <!--                @forelse($links as $id => $link)-->
    <!--                    <tr>-->
    <!--                        <td class="fw-bold text-dark">{{ $link['title'] ?? '' }}</td>-->
    <!--                        <td>-->
    <!--                            @if(!empty($link['url']))-->
    <!--                                <a href="{{ $link['url'] }}" target="_blank" class="text-decoration-none text-primary fw-semibold">-->
    <!--                                    {{ $link['url'] }}-->
    <!--                                </a>-->
    <!--                            @else-->
    <!--                                <span class="text-muted">—</span>-->
    <!--                            @endif-->
    <!--                        </td>-->
    <!--                        <td>{!! $link['description'] !!}</td>-->
    <!--                        <td class="text-center">-->
                                <!-- Edit Button -->
    <!--                            <button class="btn btn-sm btn-outline-warning me-2 rounded-pill"-->
    <!--                                    data-bs-toggle="modal"-->
    <!--                                    data-bs-target="#editModal{{ $id }}">-->
    <!--                                ✏️ Edit-->
    <!--                            </button>-->

                                <!-- Delete Form -->
                                <!--<form action="{{ route('quick-links.destroy', $id) }}" method="POST" class="d-inline">-->
                                <!--    @csrf @method('DELETE')-->
                                <!--    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Are you sure?')">-->
                                <!--        🗑 Delete-->
                                <!--    </button>-->
                                <!--</form>-->
    <!--                        </td>-->
    <!--                    </tr>-->

                        <!-- Edit Modal -->
    <!--                    <div class="modal fade" id="editModal{{ $id }}" tabindex="-1" aria-hidden="true">-->
    <!--                      <div class="modal-dialog modal-dialog-centered modal-lg">-->
    <!--                        <div class="modal-content border-0 shadow-lg rounded-4">-->
    <!--                          <form action="{{ route('quick-links.update', $id) }}" method="POST">-->
    <!--                            @csrf @method('PUT')-->
    <!--                            <div class="modal-header bg-gradient text-white rounded-top-4">-->
    <!--                              <h5 class="modal-title">✏️ Edit Quick Link</h5>-->
    <!--                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>-->
    <!--                            </div>-->
    <!--                            <div class="modal-body p-4">-->
    <!--                              <div class="mb-3">-->
    <!--                                  <label class="form-label fw-semibold">Title</label>-->
    <!--                                  <input type="text" name="title" value="{{ $link['title'] }}" class="form-control shadow-sm" required>-->
    <!--                              </div>-->
    <!--                              <div class="mb-3">-->
    <!--                                  <label class="form-label fw-semibold">URL</label>-->
    <!--                                  <input type="url" name="url" value="{{ $link['url'] ?? '' }}" class="form-control shadow-sm">-->
    <!--                              </div>-->
    <!--                              <div class="mb-3">-->
    <!--                                  <label class="form-label fw-semibold">Description</label>-->
    <!--                                  <textarea name="description" id="editor{{ $id }}" class="form-control shadow-sm" rows="4">{{ $link['description'] ?? '' }}</textarea>-->
    <!--                              </div>-->
    <!--                            </div>-->
    <!--                            <div class="modal-footer">-->
    <!--                              <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">❌ Cancel</button>-->
    <!--                              <button type="submit" class="btn btn-gradient rounded-pill px-4">💾 Update</button>-->
    <!--                            </div>-->
    <!--                          </form>-->
    <!--                        </div>-->
    <!--                      </div>-->
    <!--                    </div>-->
    <!--                @empty-->
    <!--                    <tr>-->
    <!--                        <td colspan="4" class="text-center text-muted py-4">-->
    <!--                            🚫 No quick links found. Click <strong>“Add Quick Link”</strong> to create one.-->
    <!--                        </td>-->
    <!--                    </tr>-->
    <!--                @endforelse-->
    <!--            </tbody>-->
    <!--        </table>-->
    <!--    </div>-->
    <!--</div>-->
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="{{ route('quick-links.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-gradient text-white rounded-top-4">
          <h5 class="modal-title">➕ Add Quick Link</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
              <label class="form-label fw-semibold">Title</label>
              <input type="text" name="title" class="form-control shadow-sm" required>
          </div>
          <div class="mb-3">
              <label class="form-label fw-semibold">URL</label>
              <input type="url" name="url" class="form-control shadow-sm">
          </div>
          <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" id="editor-create" class="form-control shadow-sm" rows="4"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">❌ Cancel</button>
          <button type="submit" class="btn btn-gradient rounded-pill px-4">💾 Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Custom Styles -->
<style>
    .btn-gradient {
        background: linear-gradient(135deg, #ffe700, #000000);
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .bg-gradient {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .container {
            max-width: none;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>

<!-- CKEditor Script -->
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<!-- CKEditor Script (Secure LTS Version) -->
<!--<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>-->

<script>
    // Initialize CKEditor for create modal
    CKEDITOR.replace('editor-create', {
        // ADD this line to disable the version check for the create instance
        versionCheck: false 
    });

    // Initialize CKEditor for each edit modal
    @foreach($links as $id => $link)
        CKEDITOR.replace('editor{{ $id }}', {
            versionCheck: false
        });
    @endforeach
</script>
@endsection







