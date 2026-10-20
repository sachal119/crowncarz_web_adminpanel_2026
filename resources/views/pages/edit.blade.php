@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('pages.index') }}" class="text-decoration-none text-muted" style="font-size: 13px;">
                    <i class="bi bi-arrow-left me-1"></i> Blogs
                </a>
                <span class="text-muted">/</span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(230, 176, 74, 0.15); color: #E6B04A; font-weight: 600; font-size: 11px;">
                    EDIT BLOG
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-truncate" style="letter-spacing: -0.5px; max-width: 600px;">
                Edit: {{ $page['title'] ?? 'Blog' }}
            </h3>
            <p class="text-muted mb-0" style="font-size: 13px;">Update content, featured media, search meta, and redirection settings.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(!empty($page['slug']))
                <a href="https://crowncarz.com/blogs/{{ $page['slug'] }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 py-2 d-flex align-items-center" style="border-radius: 8px;" title="View on live website">
                    <i class="bi bi-box-arrow-up-right me-1.5"></i> View Live
                </a>
            @endif
            <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 d-flex align-items-center" style="border-radius: 8px;">
                <i class="bi bi-x-lg me-1.5"></i> Cancel
            </a>
            <button type="button" onclick="submitEditBlogForm()" class="btn btn-sm px-3.5 py-2 d-flex align-items-center shadow-sm fw-semibold" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                <i class="bi bi-check2-circle me-1.5 fs-6"></i> Update Blog
            </button>
        </div>
    </div>

    {{-- Error Alerts --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #b91c1c;">
            <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i> Please fix the following errors:
            </div>
            <ul class="mb-0 ps-3" style="font-size: 13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form --}}
    <form id="editBlogForm" action="{{ route('admin.pages.update', $id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Hidden current image url --}}
        <input type="hidden" name="old_featured_image" value="{{ $page['featured_image'] ?? '' }}" />

        <div class="row g-4">
            {{-- Left Column: Main Content & SEO --}}
            <div class="col-lg-8">
                {{-- Blog Content Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header border-bottom p-3.5 d-flex align-items-center justify-content-between" style="background: transparent; border-color: var(--cc-border) !important;">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--cc-text-primary);">
                            <i class="bi bi-file-earmark-richtext" style="color: #E6B04A;"></i> Article Content
                        </h6>
                        <span class="text-muted" style="font-size: 12px;">* Required fields</span>
                    </div>

                    <div class="card-body p-3.5">
                        {{-- Title --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">
                                Blog Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="blogTitleInput" class="form-control form-control-lg" placeholder="e.g. 10 Best Chauffeur Services in London for Corporate Travel" value="{{ old('title', $page['title'] ?? '') }}" required style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 10px; font-size: 16px;" oninput="syncSlug(this.value)" />
                        </div>

                        {{-- URL Slug --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center" style="font-size: 13px; color: var(--cc-text-primary);">
                                <span>URL Slug <span class="text-muted fw-normal">(permalink)</span></span>
                                <small class="text-muted" id="slugPreviewText">https://crowncarz.com/blogs/{{ $page['slug'] ?? '...' }}</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text font-monospace" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-muted); font-size: 13px;">
                                    crowncarz.com/blogs/
                                </span>
                                <input type="text" name="slug" id="blogSlugInput" class="form-control font-monospace" placeholder="my-blog-post-slug" value="{{ old('slug', $page['slug'] ?? '') }}" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); font-size: 13px;" />
                                <button type="button" class="btn btn-outline-secondary" onclick="regenerateSlug()" title="Regenerate slug from title">
                                    <i class="bi bi-magic"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Rich Text Content --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <label class="form-label fw-semibold mb-0" style="font-size: 13px; color: var(--cc-text-primary);">
                                    Body Content <span class="text-danger">*</span>
                                </label>
                                <span class="badge rounded-pill" id="editorWordCountBadge" style="background: var(--cc-bg-main); color: var(--cc-text-muted); font-size: 11px; border: 1px solid var(--cc-border);">
                                    0 words
                                </span>
                            </div>
                            <textarea name="content" id="blogEditor" rows="14" class="form-control" placeholder="Write your blog post content here...">{{ old('content', $page['content'] ?? '') }}</textarea>
                            <div id="contentValidationError" class="text-danger mt-1 fw-semibold" style="font-size: 12px; display: none;"></div>
                        </div>
                    </div>
                </div>

                {{-- SEO & Search Optimization Card --}}
                <div class="card border-0 shadow-sm" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header border-bottom p-3.5" style="background: transparent; border-color: var(--cc-border) !important;">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--cc-text-primary);">
                            <i class="bi bi-search" style="color: #E6B04A;"></i> Search Engine Optimization (SEO)
                        </h6>
                    </div>

                    <div class="card-body p-3.5">
                        <div class="row g-3">
                            {{-- Focus Keyword --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">
                                    Focus Keyword
                                </label>
                                <input type="text" name="focus_keyword" class="form-control" placeholder="e.g. airport transfers manchester" value="{{ old('focus_keyword', $page['focus_keyword'] ?? '') }}" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" />
                            </div>

                            {{-- Canonical URL --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">
                                    Canonical URL
                                </label>
                                <input type="url" name="canonical_url" class="form-control" placeholder="https://crowncarz.com/blogs/..." value="{{ old('canonical_url', $page['canonical_url'] ?? '') }}" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" />
                            </div>

                            {{-- Meta Title --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0" style="font-size: 13px; color: var(--cc-text-primary);">
                                        Meta Title
                                    </label>
                                    <small class="text-muted" id="metaTitleCount">0 / 60 chars</small>
                                </div>
                                <input type="text" name="meta_title" id="metaTitleInput" maxlength="120" class="form-control" placeholder="SEO Title that appears in Google search" value="{{ old('meta_title', $page['meta_title'] ?? '') }}" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" oninput="updateCharCount('metaTitleInput', 'metaTitleCount', 60)" />
                            </div>

                            {{-- Meta Description --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0" style="font-size: 13px; color: var(--cc-text-primary);">
                                        Meta Description
                                    </label>
                                    <small class="text-muted" id="metaDescCount">0 / 160 chars</small>
                                </div>
                                <textarea name="meta_description" id="metaDescInput" rows="3" maxlength="320" class="form-control" placeholder="Brief summary of article shown under Google search result snippet..." style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" oninput="updateCharCount('metaDescInput', 'metaDescCount', 160)">{{ old('meta_description', $page['meta_description'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Publish, Image, Redirection --}}
            <div class="col-lg-4">
                {{-- Publish Settings Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header border-bottom p-3.5" style="background: transparent; border-color: var(--cc-border) !important;">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--cc-text-primary);">
                            <i class="bi bi-send-check" style="color: #E6B04A;"></i> Publishing
                        </h6>
                    </div>

                    <div class="card-body p-3.5">
                        {{-- Status Select --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">Status</label>
                            @php
                                $currentStatus = old('status', $page['status'] ?? 'Published');
                            @endphp
                            <select name="status" class="form-select" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;">
                                <option value="Published" {{ $currentStatus === 'Published' ? 'selected' : '' }}>🟢 Published</option>
                                <option value="Draft" {{ $currentStatus === 'Draft' ? 'selected' : '' }}>🟡 Draft (Hidden)</option>
                            </select>
                        </div>

                        {{-- Metadata timestamps --}}
                        @if(!empty($page['created_at']) || !empty($page['updated_at']))
                            <div class="p-2.5 rounded mb-3" style="background: var(--cc-bg-main); border: 1px solid var(--cc-border); font-size: 12px;">
                                @if(!empty($page['created_at']))
                                    <div class="text-muted d-flex justify-content-between mb-1">
                                        <span>Created:</span>
                                        <span class="fw-semibold text-end">{{ date('d M Y, H:i', strtotime($page['created_at'])) }}</span>
                                    </div>
                                @endif
                                @if(!empty($page['updated_at']))
                                    <div class="text-muted d-flex justify-content-between">
                                        <span>Updated:</span>
                                        <span class="fw-semibold text-end">{{ date('d M Y, H:i', strtotime($page['updated_at'])) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" id="updateBlogBtn" class="btn fw-semibold py-2.5 shadow-sm" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                                <i class="bi bi-check2-circle me-1.5"></i> Update Blog Post
                            </button>
                            <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary py-2" style="border-radius: 8px; font-size: 13px;">
                                Cancel & Return
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Featured Cover Image Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header border-bottom p-3.5" style="background: transparent; border-color: var(--cc-border) !important;">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--cc-text-primary);">
                            <i class="bi bi-card-image" style="color: #E6B04A;"></i> Featured Image
                        </h6>
                    </div>

                    <div class="card-body p-3.5">
                        {{-- Image Preview Box --}}
                        @php
                            $currentImg = $page['featured_image'] ?? null;
                        @endphp
                        <div id="imagePreviewContainer" class="rounded mb-3 d-flex flex-column align-items-center justify-content-center p-2 text-center position-relative" style="background: var(--cc-bg-main); border: 2px dashed var(--cc-border); min-height: 160px; overflow: hidden;">
                            <img id="imagePreview" src="{{ $currentImg ?? '#' }}" alt="Preview" class="rounded w-100 object-fit-cover" style="{{ $currentImg ? 'display: block;' : 'display: none;' }} max-height: 200px;" />
                            <div id="imagePlaceholder" class="text-muted" style="{{ $currentImg ? 'display: none;' : 'display: block;' }}">
                                <i class="bi bi-cloud-upload fs-1 d-block mb-1" style="color: #E6B04A;"></i>
                                <span style="font-size: 12px;">Choose JPEG, PNG, or WEBP image</span>
                            </div>
                        </div>

                        {{-- File Input to Replace --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">
                                {{ $currentImg ? 'Replace Image (optional)' : 'Upload Image' }}
                            </label>
                            <input type="file" name="featured_image" id="featuredImageInput" class="form-control form-control-sm" accept="image/*" onchange="previewSelectedImage(this)" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" />
                        </div>

                        {{-- Alt Text --}}
                        <div>
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">Image Alt Text</label>
                            <input type="text" name="image_alt" class="form-control form-control-sm" placeholder="Descriptive text for accessibility & SEO" value="{{ old('image_alt', $page['image_alt'] ?? '') }}" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" />
                        </div>
                    </div>
                </div>

                {{-- Redirection Settings Card --}}
                <div class="card border-0 shadow-sm" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header border-bottom p-3.5" style="background: transparent; border-color: var(--cc-border) !important;">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--cc-text-primary);">
                            <i class="bi bi-signpost-split" style="color: #E6B04A;"></i> Redirection (Optional)
                        </h6>
                    </div>

                    <div class="card-body p-3.5">
                        {{-- Redirect Type --}}
                        @php
                            $currentRedirect = old('redirect_type', $page['redirect_type'] ?? 'none');
                        @endphp
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">Redirect Type</label>
                            <select name="redirect_type" id="redirectTypeSelect" class="form-select form-select-sm" onchange="handleRedirectChange(this.value)" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;">
                                <option value="none" {{ $currentRedirect === 'none' ? 'selected' : '' }}>None (Standard Page)</option>
                                <option value="301" {{ $currentRedirect === '301' ? 'selected' : '' }}>301 Permanent Redirect</option>
                                <option value="302" {{ $currentRedirect === '302' ? 'selected' : '' }}>302 Temporary Redirect</option>
                                <option value="404" {{ $currentRedirect === '404' ? 'selected' : '' }}>404 Not Found</option>
                            </select>
                        </div>

                        {{-- Redirect URL --}}
                        <div id="redirectUrlGroup" style="{{ in_array($currentRedirect, ['301', '302']) ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--cc-text-primary);">Target Redirect URL</label>
                            <input type="url" name="redirect_url" class="form-control form-control-sm" placeholder="https://example.com/target" value="{{ old('redirect_url', $page['redirect_url'] ?? '') }}" style="background: var(--cc-bg-main); border-color: var(--cc-border); color: var(--cc-text-primary); border-radius: 8px;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- CKEditor & Scripts --}}
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    // Initialize CKEditor
    if (document.getElementById('blogEditor')) {
        CKEDITOR.replace('blogEditor', {
            versionCheck: false,
            allowedContent: true,
            height: 380,
            toolbar: [
                { name: 'document', items: [ 'Source' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                '/',
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize' ] }
            ]
        });

        CKEDITOR.instances.blogEditor.on('instanceReady', function() {
            updateWordCount();
        });

        CKEDITOR.instances.blogEditor.on('change', function() {
            updateWordCount();
        });
    }

    function updateWordCount() {
        if (!CKEDITOR.instances.blogEditor) return;
        const text = CKEDITOR.instances.blogEditor.getData().replace(/<[^>]*>/g, '').trim();
        const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
        const badge = document.getElementById('editorWordCountBadge');
        if (badge) {
            badge.textContent = words + (words === 1 ? ' word' : ' words');
        }
    }

    // Auto slug synchronization
    let isSlugManual = true; // In edit mode, preserve existing slug by default
    const slugInput = document.getElementById('blogSlugInput');
    const slugPreviewText = document.getElementById('slugPreviewText');

    if (slugInput) {
        slugInput.addEventListener('input', function() {
            isSlugManual = true;
            updateSlugPreview(this.value);
        });
    }

    function syncSlug(val) {
        if (!isSlugManual && slugInput) {
            const slug = toSlug(val);
            slugInput.value = slug;
            updateSlugPreview(slug);
        }
    }

    function regenerateSlug() {
        const title = document.getElementById('blogTitleInput').value;
        const slug = toSlug(title);
        if (slugInput) {
            slugInput.value = slug;
            updateSlugPreview(slug);
        }
    }

    function updateSlugPreview(val) {
        if (slugPreviewText) {
            slugPreviewText.textContent = 'https://crowncarz.com/blogs/' + (val || '...');
        }
    }

    function toSlug(str) {
        return (str || '')
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // Image preview
    function previewSelectedImage(input) {
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Redirect options handler
    function handleRedirectChange(val) {
        const group = document.getElementById('redirectUrlGroup');
        if (group) {
            group.style.display = (val === '301' || val === '302') ? 'block' : 'none';
        }
    }

    // Character counter initialization
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCount('metaTitleInput', 'metaTitleCount', 60);
        updateCharCount('metaDescInput', 'metaDescCount', 160);
    });

    function updateCharCount(inputId, countId, recommendedMax) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        if (input && counter) {
            const len = input.value.length;
            counter.textContent = len + ' / ' + recommendedMax + ' chars';
            counter.style.color = (len > recommendedMax) ? '#f59e0b' : '';
        }
    }

    // Form submit
    function submitEditBlogForm() {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.blogEditor) {
            CKEDITOR.instances.blogEditor.updateElement();
        }

        const title = document.getElementById('blogTitleInput');
        if (!title.value.trim()) {
            title.focus();
            alert('Please enter a blog title.');
            return;
        }

        const form = document.getElementById('editBlogForm');
        const updateBtn = document.getElementById('updateBlogBtn');
        if (updateBtn) {
            updateBtn.disabled = true;
            updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span> Updating Blog...';
        }

        form.submit();
    }
</script>
@endsection