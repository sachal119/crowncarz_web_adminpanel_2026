@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.page-card {
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.table thead {
    background: #f8f9fa;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.action-btn {
    border-radius: 8px;
    padding: 5px 10px;
}

.search-box {
    width: 250px;
}
</style>

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">📄 Blog Pages</h3>
            <small class="text-muted">Manage CrownCarz blog pages</small>
        </div>

        <a href="{{ route('admin.pages.create') }}" class="btn shadow-sm" style="background-color: darkgoldenrod;border: darkgoldenrod;color: black;">
            <i class="fas fa-plus"></i> Add New Blog
        </a>
    </div>

    <!-- Search + Stats -->
    <div class="d-flex justify-content-between mb-3">

        <input type="text" id="searchInput" class="form-control search-box" placeholder="Search blog...">

        <span class="text-muted">
            Total Blogs: <strong>{{ count($pages) }}</strong>
        </span>

    </div>

    <!-- Table -->
    <div class="card page-card">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <!--<th>Status</th>-->
                            <th width="200" class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="blogTable">

                        @forelse($pages as $id => $page)

                        <tr>

                            <td class="fw-semibold">
                                {{ $page['title'] }}
                            </td>

                            <td class="text-muted">
                                /{{ $page['slug'] }}
                            </td>

                            <!--<td>-->
                            <!--    <span class="status-badge badge -->
                            <!--        {{ ($page['status'] ?? 'Draft') == 'Published' ? 'bg-success' : 'bg-secondary' }}">-->
                            <!--        {{ $page['status'] ?? 'Draft' }}-->
                            <!--    </span>-->
                            <!--</td>-->

                            <td class="text-center">

                                <!-- View -->
                                <a href="https://crowncarz.com/blogs/{{ $page['slug'] }}"
   target="_blank"
   class="btn btn-sm action-btn" style="background-color: darkgoldenrod;border: darkgoldenrod;">
    <i class="fas fa-eye"></i>
</a>

<a href="https://crowncarz.com/admin/blogs/pages/edit/{{$id}}" target="_blank"
   class="btn btn-warning btn-sm action-btn">
    <i class="fas fa-edit"></i>
</a>
                                <!-- Delete -->
                                <button 
                                    class="btn btn-danger btn-sm action-btn"
                                    onclick="deleteBlog('{{ $id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="4" class="text-center py-4">
                                No Blogs Found
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>


<!-- Delete Script -->
<script>

function deleteBlog(id)
{
    if(confirm("Are you sure you want to delete this blog?"))
    {
        window.location.href = "https://crowncarz.com/admin/blogs/pages/delete/" + id;
    }
}

// Search Filter
document.getElementById("searchInput").addEventListener("keyup", function() {

    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#blogTable tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });

});

</script>

@endsection