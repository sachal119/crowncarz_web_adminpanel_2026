@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Breadcrumb & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(230, 176, 74, 0.15); color: #E6B04A; font-weight: 600; font-size: 11px;">
                    <i class="bi bi-journal-richtext me-1"></i> CONTENT MANAGEMENT
                </span>
            </div>
            <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Blogs & Pages</h3>
            <p class="text-muted mb-0" style="font-size: 13px;">Manage, edit, publish, and optimize blogs and landing pages for Crown Carz.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="https://crowncarz.com/blogs" target="_blank" class="btn btn-outline-secondary btn-sm px-3 py-2 d-flex align-items-center" style="border-radius: 8px;">
                <i class="bi bi-box-arrow-up-right me-1.5"></i> Visit Blog Page
            </a>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-sm px-3.5 py-2 d-flex align-items-center shadow-sm fw-semibold" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                <i class="bi bi-plus-circle-fill me-1.5 fs-6"></i> Add New Blog
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 d-flex align-items-center" role="alert" style="border-radius: 10px; background: rgba(34, 197, 94, 0.12); color: #15803d;">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3 d-flex align-items-center" role="alert" style="border-radius: 10px; background: rgba(239, 68, 68, 0.12); color: #b91c1c;">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics Summary Cards --}}
    @php
        $totalPages = count($pages);
        $publishedCount = 0;
        $draftCount = 0;
        foreach ($pages as $p) {
            $st = $p['status'] ?? 'Published';
            if ($st === 'Published') {
                $publishedCount++;
            } else {
                $draftCount++;
            }
        }
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Total Blogs</div>
                        <h3 class="fw-bold mb-0 mt-1" style="color: var(--cc-text-primary);">{{ $totalPages }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(230, 176, 74, 0.15); color: #E6B04A;">
                        <i class="bi bi-journals fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Published</div>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ $publishedCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(34, 197, 94, 0.15); color: #16a34a;">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Drafts</div>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">{{ $draftCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.15); color: #d97706;">
                        <i class="bi bi-file-earmark-diff fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Blog Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 14px; background: var(--cc-bg-card);">
        {{-- Search & Filter Header --}}
        <div class="card-header border-bottom p-3" style="background: transparent; border-color: var(--cc-border) !important;">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-end-0" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-muted);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control form-control-sm border-start-0" placeholder="Search by title, slug, or keyword..." style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary);" />
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 d-flex justify-content-sm-end gap-2">
                    <select id="statusFilter" class="form-select form-select-sm" style="width: auto; background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;">
                        <option value="all">All Statuses</option>
                        <option value="published">Published ({{ $publishedCount }})</option>
                        <option value="draft">Draft ({{ $draftCount }})</option>
                    </select>

                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()" title="Reset filters" style="border-radius: 8px;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="blogsMainTable" style="color: var(--cc-text-primary);">
                <thead style="background: var(--cc-table-th-bg); color: var(--cc-table-th-color); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 70px;">Cover</th>
                        <th class="py-3">Title & URL Slug</th>
                        <th class="py-3 d-none d-md-table-cell">Focus Keyword</th>
                        <th class="py-3 text-center" style="width: 140px;">Status</th>
                        <th class="py-3 d-none d-lg-table-cell">Date</th>
                        <th class="pe-3 py-3 text-end" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="blogTableBody">
                    @forelse($pages as $id => $page)
                        @php
                            $status = $page['status'] ?? 'Published';
                            $title = $page['title'] ?? 'Untitled';
                            $slug = $page['slug'] ?? '';
                            $featuredImage = $page['featured_image'] ?? null;
                            $focusKeyword = $page['focus_keyword'] ?? null;
                            $createdAt = !empty($page['created_at']) ? date('d M Y', strtotime($page['created_at'])) : (!empty($page['updated_at']) ? date('d M Y', strtotime($page['updated_at'])) : '—');
                        @endphp
                        <tr class="blog-row" data-status="{{ strtolower($status) }}" data-search="{{ strtolower($title . ' ' . $slug . ' ' . $focusKeyword) }}">
                            {{-- Cover Thumbnail --}}
                            <td class="ps-3">
                                @if($featuredImage)
                                    <img src="{{ $featuredImage }}" alt="{{ $page['image_alt'] ?? $title }}" class="rounded shadow-sm object-fit-cover" style="width: 44px; height: 44px; border: 1px solid var(--cc-border);" onerror="this.onerror=null; this.src='https://placehold.co/44x44?text=Img';" />
                                @else
                                    <div class="rounded d-flex align-items-center justify-content-center text-muted" style="width: 44px; height: 44px; background: var(--cc-bg-main); border: 1px solid var(--cc-border);">
                                        <i class="bi bi-image fs-5"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- Title & Slug --}}
                            <td>
                                <div class="fw-semibold text-truncate" style="max-width: 320px;" title="{{ $title }}">
                                    <a href="{{ route('admin.pages.edit', $id) }}" class="text-decoration-none" style="color: var(--cc-text-primary);">
                                        {{ $title }}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 mt-1" style="font-size: 12px;">
                                    <span class="text-muted font-monospace">/{{ $slug }}</span>
                                    <button type="button" class="btn btn-link p-0 text-muted" onclick="copySlug('https://crowncarz.com/blogs/{{ $slug }}', this)" title="Copy full URL" style="font-size: 12px; line-height: 1;">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </td>

                            {{-- Focus Keyword --}}
                            <td class="d-none d-md-table-cell">
                                @if($focusKeyword)
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(230, 176, 74, 0.15); color: #E6B04A; font-weight: 500; font-size: 11px;">
                                        <i class="bi bi-tag-fill me-1"></i>{{ $focusKeyword }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Status Toggle --}}
                            <td class="text-center">
                                <form action="{{ route('admin.pages.toggleStatus', $id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($status === 'Published')
                                        <button type="submit" class="badge rounded-pill border-0 px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="background: rgba(34, 197, 94, 0.15); color: #16a34a; font-weight: 600; font-size: 11px; cursor: pointer;" title="Click to switch to Draft">
                                            <i class="bi bi-check-circle-fill"></i> Published
                                        </button>
                                    @else
                                        <button type="submit" class="badge rounded-pill border-0 px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="background: rgba(245, 158, 11, 0.15); color: #d97706; font-weight: 600; font-size: 11px; cursor: pointer;" title="Click to switch to Published">
                                            <i class="bi bi-file-earmark-diff"></i> Draft
                                        </button>
                                    @endif
                                </form>
                            </td>

                            {{-- Created Date --}}
                            <td class="d-none d-lg-table-cell text-muted" style="font-size: 12px;">
                                {{ $createdAt }}
                            </td>

                            {{-- Actions --}}
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    {{-- View Live --}}
                                    <a href="https://crowncarz.com/blogs/{{ $slug }}" target="_blank" class="btn btn-outline-secondary" title="View live blog on website" style="border-radius: 6px 0 0 6px;">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.pages.edit', $id) }}" class="btn btn-outline-primary" title="Edit this blog">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button" class="btn btn-outline-danger" title="Delete blog" onclick="confirmBlogDelete('{{ $id }}', '{{ addslashes($title) }}')" style="border-radius: 0 6px 6px 0;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyTableRow">
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: rgba(230, 176, 74, 0.15); color: #E6B04A;">
                                        <i class="bi bi-journal-plus fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">No blogs created yet</h5>
                                    <p class="text-muted mb-3" style="font-size: 13px;">Get started by publishing your first blog post for Crown Carz.</p>
                                    <a href="{{ route('admin.pages.create') }}" class="btn btn-sm px-3.5 py-2 fw-semibold" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                                        <i class="bi bi-plus-circle-fill me-1"></i> Add New Blog
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-search me-1"></i> No matching blogs found for your search query.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteBlogModal" tabindex="-1" aria-labelledby="deleteBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; background: var(--cc-modal-bg); color: var(--cc-text-primary);">
            <div class="modal-header border-0 pb-0">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <h5 class="fw-bold mb-1" id="deleteBlogModalLabel">Delete Blog</h5>
                <p class="text-muted mb-2" style="font-size: 13px;">Are you sure you want to delete this blog? This action cannot be undone.</p>
                <div class="p-2.5 rounded text-truncate fw-semibold" id="deleteBlogTitle" style="background: var(--cc-bg-main); border: 1px solid var(--cc-border); font-size: 13px;">
                    Blog Title
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <form id="deleteBlogForm" method="POST" action="" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger px-3 d-flex align-items-center" style="border-radius: 8px;">
                        <i class="bi bi-trash3 me-1.5"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Scripts for Search, Filter, Copy, and Delete --}}
<script>
    function confirmBlogDelete(id, title) {
        document.getElementById('deleteBlogTitle').textContent = title;
        document.getElementById('deleteBlogForm').action = "{{ url('blogs/pages/delete') }}/" + encodeURIComponent(id);
        const modal = new bootstrap.Modal(document.getElementById('deleteBlogModal'));
        modal.show();
    }

    function copySlug(url, btn) {
        navigator.clipboard.writeText(url).then(function() {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
            setTimeout(function() {
                btn.innerHTML = originalIcon;
            }, 1500);
        });
    }

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');

    function filterTable() {
        const query = (searchInput.value || '').toLowerCase().trim();
        const selectedStatus = (statusFilter.value || 'all').toLowerCase();
        const rows = document.querySelectorAll('.blog-row');
        let visibleCount = 0;

        rows.forEach(function(row) {
            const rowSearch = row.getAttribute('data-search') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesQuery = !query || rowSearch.includes(query);
            const matchesStatus = (selectedStatus === 'all') || (rowStatus === selectedStatus);

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) {
            if (visibleCount === 0 && rows.length > 0) {
                noResultsRow.style.display = '';
            } else {
                noResultsRow.style.display = 'none';
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }

    function resetFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        filterTable();
    }
</script>
@endsection