@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Page</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('pages','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('pages','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','pages');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('pages.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Page List</h3>
                        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table id="responsive-datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="4%"><input name="checkbox" onclick='checkedAll();' type="checkbox" readonly="readonly" /></th>
                                            <th>SI</th>
                                            <th>Title</th>
                                            <th>Parent</th>
                                            <th>Sequence</th>
                                            <th>Page Structure</th>
                                            <th>Connected Page</th>
                                            <th>External URL</th>
                                            <th>Menu Type</th>
                                            <th>Homepage Display</th>
                                            <th>Why Choose</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pages as $page)
                                        <tr id="tablerow{{ $page->id }}" class="tablerow">
                                            <td><input type="checkbox" name="summe_code[]" id="summe_code{{ $page->id }}" value="{{ $page->id }}" /></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $page->title }}</td>
                                            <td>{{ $page->parent ? $page->parent->title : '—' }}</td>
                                            <td>{{ $page->sequence }}</td>
                                            <td>{{ $page->page_structure }}</td>
                                            <td>{{ $page->connected_page }}</td>
                                            <td>
                                                @if($page->external_url)
                                                <a href="{{ $page->external_url }}" target="_blank">{{ $page->external_url }}</a>
                                                @else
                                                N/A
                                                @endif
                                            </td>
                                            <td>{{ $page->menu_type }}</td>

                                            <td>
                                                @if($page->homepage_display)
                                                <span class="badge text-bg-success">Yes</span>
                                                @else
                                                <span class="badge text-bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($page->why_choose)
                                                <span class="badge text-bg-success">Yes</span>
                                                @else
                                                <span class="badge text-bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! $page->status ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Page actions">
                                                    <a class="btn btn-outline-success btn-sm edit-sequence-btn"
                                                        data-id="{{ $page->id }}"
                                                        data-bs-toggle="tooltip"
                                                        title="Set Sequence"
                                                        data-title="{{ $page->title }}"
                                                        data-sequence="{{ $page->sequence }}">
                                                        <i class="bi bi-list-nested"></i>
                                                    </a>
                                                    <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="{{ route('pages.show', $page->id) }}" class="btn btn-outline-info btn-sm" data-bs-toggle="tooltip" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="deleteSingle('{{ $page->id }}','masterdelete','pages')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </form>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $pages->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Sequence Modal -->
    <div class="modal fade" id="editSequenceModal" tabindex="-1" aria-labelledby="editSequenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSequenceModalLabel">Edit Page Sequence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSequenceForm">
                        @csrf
                        <div class="mb-3">
                            <label for="pageTitle" class="form-label">Page Title</label>
                            <input type="text" class="form-control" id="pageTitle" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="newSequence" class="form-label">New Sequence</label>
                            <input type="number" class="form-control" id="newSequence" name="sequence" required>
                        </div>
                        <input type="hidden" id="pageId" name="id">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin/layouts.footer')
</div>

@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Open modal and populate fields when "Edit" button is clicked
        $(".edit-sequence-btn").on("click", function() {
            var pageId = $(this).data("id");
            var pageTitle = $(this).data("title");
            var currentSequence = $(this).data("sequence");

            // Set modal fields
            $("#pageId").val(pageId);
            $("#pageTitle").val(pageTitle);
            $("#newSequence").val(currentSequence);

            // Show modal
            var myModal = new bootstrap.Modal($("#editSequenceModal"));
            myModal.show();
        });

        // Handle form submission
        $("#editSequenceForm").on("submit", function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('page.updateSequence') }}",
                method: "POST",
                data: formData,
                success: function(response) {
                    // Close modal
                    var myModal = bootstrap.Modal.getInstance($("#editSequenceModal"));
                    myModal.hide();
                    window.location.reload();
                },
                error: function(xhr) {
                    // Handle error
                    alert("An error occurred while updating the sequence.");
                }
            });
        });
    });
</script>
@endsection