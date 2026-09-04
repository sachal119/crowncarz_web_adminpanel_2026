@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h3 class="mb-4">Edit Blog</h3>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.pages.update', $id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>Page Title</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ $page['title'] }}" required>
                </div>

                <div class="mb-3">
                    <label>Slug</label>
                    <input type="text" name="slug" class="form-control"
                           value="{{ $page['slug'] }}">
                </div>

                <div class="mb-3">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title"
                           class="form-control"
                           value="{{ $page['meta_title'] }}">
                </div>

                <div class="mb-3">
                    <label>Meta Description</label>
                    <textarea name="meta_description"
                              class="form-control">{{ $page['meta_description'] }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Canonical URL</label>
                    <input type="text" name="canonical_url"
                           class="form-control"
                           value="{{ $page['canonical_url'] }}">
                </div>

                <div class="mb-3">
                    <label>Content</label>
                    <textarea name="content"
                              class="form-control"
                              rows="8">{{ $page['content'] }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Featured Image</label>

                    <input type="file"
                           name="featured_image"
                           class="form-control">

                    <br>

                    @if(isset($page['featured_image']))
                        <img src="{{ $page['featured_image'] }}"
                             width="150">
                    @endif

                    <input type="hidden"
                           name="old_featured_image"
                           value="{{ $page['featured_image'] }}">
                </div>

                <div class="mb-3">
                    <label>Image ALT</label>
                    <input type="text"
                           name="image_alt"
                           class="form-control"
                           value="{{ $page['image_alt'] }}">
                </div>

                <button class="btn btn-success">
                    Update Blog
                </button>

                <a href="{{ route('pages.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

@endsection