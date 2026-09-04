@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Add New Page</h3>
        <a href="{{ route('pages.index') }}" class="btn btn-secondary">Back to Pages</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="pageForm" method="POST" action="{{ url('https://crowncarz.com/admin/pages/store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Page Title & Slug -->
                <div class="mb-3">
                    <label class="form-label">Page Title <span class="text-danger">*</span></label>
                    <input class="form-control" name="title" placeholder="Page Title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug (optional)</label>
                    <input class="form-control" name="slug" placeholder="Page URL/Slug">
                </div>

                <!-- SEO Meta -->
                <h5 class="mt-4">SEO Meta</h5>
                <div class="mb-3">
                    <label class="form-label">Meta Title (60 chars)</label>
                    <input class="form-control" name="meta_title" placeholder="Meta Title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Focus Keyword</label>
                    <input class="form-control" name="focus_keyword" placeholder="Focus Keyword">
                </div>
                <div class="mb-3">
                    <label class="form-label">Canonical URL</label>
                    <input class="form-control" name="canonical_url" placeholder="https://example.com/page">
                </div>

                <!-- Content -->
                <h5 class="mt-4">Content</h5>
                <div class="mb-3">
                    <textarea name="content" id="editor" class="form-control" rows="10"></textarea>
                    <div id="contentErrors" class="text-danger mt-1"></div>
                </div>

                <!-- Redirects -->
                <h5 class="mt-4">Redirect</h5>
                <div class="mb-3">
                    <label class="form-label">Redirect Type</label>
                    <select class="form-control" name="redirect_type">
                        <option value="none">None</option>
                        <option value="301">301</option>
                        <option value="302">302</option>
                        <option value="404">404</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Redirect URL</label>
                    <input class="form-control" name="redirect_url" placeholder="https://example.com/redirect">
                </div>

                <!-- Featured Image -->
                <h5 class="mt-4">Featured Image (WEBP)</h5>
                <div class="mb-3">
                    <input type="file" name="featured_image" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Image ALT Text</label>
                    <input class="form-control" name="image_alt" placeholder="Image ALT">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success mt-3">Save Page</button>
            </form>
        </div>
    </div>
</div>

<!-- CKEditor Script -->
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        versionCheck: false,
        allowedContent: true
    });

    const form = document.getElementById('pageForm');
    const errorDiv = document.getElementById('contentErrors');

    form.addEventListener('submit', function(e) {
        errorDiv.innerHTML = '';
        const content = CKEDITOR.instances.editor.getData();

        // 1️⃣ Minimum 500 words
        const wordCount = content.replace(/<[^>]*>/g, '').trim().split(/\s+/).filter(Boolean).length;
        if (wordCount < 500) {
            errorDiv.innerHTML = 'Content must be at least 500 words. Currently: ' + wordCount;
            e.preventDefault();
            return false;
        }

        // 2️⃣ Exactly 1 H1
        // const h1Count = (content.match(/<h1[^>]*>/gi) || []).length;
        // if (h1Count !== 1) {
        //     errorDiv.innerHTML = 'Content must contain exactly 1 H1. Currently: ' + h1Count;
        //     e.preventDefault();
        //     return false;
        // }

        // // 3️⃣ Maximum 5 H2
        // const h2Count = (content.match(/<h2[^>]*>/gi) || []).length;
        // if (h2Count > 5) {
        //     errorDiv.innerHTML = 'Content can contain max 5 H2 headings. Currently: ' + h2Count;
        //     e.preventDefault();
        //     return false;
        // }
    });
</script>
@endsection
