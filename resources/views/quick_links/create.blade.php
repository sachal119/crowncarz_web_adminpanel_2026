<form method="POST" action="{{ isset($quickLink) ? route('quick-links.update', $quickLink) : route('quick-links.store') }}">
    @csrf
    @if(isset($quickLink)) @method('PUT') @endif

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" value="{{ $quickLink->title ?? old('title') }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>URL (optional)</label>
        <input type="url" name="url" value="{{ $quickLink->url ?? old('url') }}" class="form-control">
    </div>

    <div class="mb-3">
        <label>Content (optional)</label>
        <textarea name="content" class="form-control">{{ $quickLink->content ?? old('content') }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">💾 Save</button>
</form>
