@extends('auth.subscribers.layouts.app')
@section('content')

<div class="container py-4">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <h3 class="mb-4">Folders</h3>

    <!-- Create Folder Form -->
    <div class="mb-4 d-flex align-items-center gap-2">
        @csrf
        <div class="input-group search-input-group position-relative">
            <i class="bi bi-search position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);"></i>

            <input type="text"
                id="folderSearch"
                class="form-control"
                name="folder"
                placeholder="Search in My Folder"
                oninput="searchFolder(this.value)"
                style="padding-left: 40px !important; border-radius:30px !important; background-color:#D6DFEA !important">

            <button type="button"
                id="clearFolderBtn"
                class="btn"
                onclick="clearFolderSearch()"
                style="margin-left: 10px; display: none;">
                Clear
            </button>

            <a href="#"
                class="book-now-btn"
                style="border-radius:999px; margin-left:20px"
                data-bs-toggle="modal"
                data-bs-target="#newFolderModal">
                <i class="bi bi-plus"></i> New
            </a>
        </div>




    </div>



    <!-- View toggle buttons -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-outline-secondary me-2" id="gridViewBtn"><i class="bi bi-grid-fill"></i></button>
        <button class="btn btn-outline-secondary" id="listViewBtn"><i class="bi bi-list"></i></button>
    </div>

    <!-- Grid View -->
    <section id="gridView" class="mb-5">
        <div class="row g-3">
            <h3 class="mb-2">My Folders</h3>
            @forelse($folders as $folder)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card p-3 position-relative shadow-sm folder-item text-start">
                    <a href="{{ route('subscriber.files.index', Crypt::encrypt($folder->id)) }}" class="text-decoration-none text-dark d-flex flex-column align-items-start">
                        <img src="{{ asset('frontend/assets/img/folder.png') }}" class="folder-icon mb-2">
                        <div class="small text-truncate">{{ $folder->name }}</div>
                        <!-- <div class="text-muted small">{{ $folder->files_count ?? $folder->files->count() }} files</div> -->
                        <div class="text-muted small">{{ $folder->decision_count }} decisions</div>
                    </a>

                    <div class="position-absolute top-0 end-0 m-1 dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end custom-dropdown">

                            <li>
                                <a href="{{ route('subscriber.folder.download', Crypt::encrypt($folder->id)) }}" class="dropdown-item">
                                    <i class="bi bi-download me-1"></i> Download Folder
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="dropdown-item edit-folder-btn"
                                    data-id="{{ $folder->id }}"
                                    data-name="{{ $folder->name }}"
                                    data-action="{{ route('subscriber.folder.update', $folder->id) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editFolderModal">
                                    <i class="bi bi-pencil me-1"></i> Rename
                                </a>
                            </li>

                            <li>
                                <form action="{{ route('subscriber.folder.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this folder?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-1"></i> Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            @empty
            <p>No folders yet.</p>
            @endforelse
        </div>


        <div class="row g-3 mt-5">
            <h3 class="mb-2">Shared Files</h3>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="card p-3 position-relative shadow-sm folder-item text-start">
                    <a href="{{ route('subscriber.folders.shared') }}" class="text-decoration-none text-dark d-flex flex-column align-items-start">
                        <img src="{{ asset('frontend/assets/img/folder.png') }}" class="folder-icon mb-2">
                        <div class="small text-truncate">Shared Folder</div>
                        <div class="text-muted small">{{ $sharedDecisions }} decisions</div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- List View -->
    <section id="listView" class="d-none">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>My Folders</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($folders as $folder)
                    <tr class="folder-row">
                        <td>
                            <a href="{{ route('subscriber.files.index', Crypt::encrypt($folder->id)) }}" class="d-flex align-items-center text-decoration-none">
                                <img src="{{ asset('frontend/assets/img/folder.png') }}" class="me-3" style="width: 40px; height: 40px;">

                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $folder->name }}</span>
                                    <small class="text-muted">{{ $folder->decision_count }} decisions</small>
                                </div>
                            </a>

                        </td>
                        <td class="text-end">
                            <a href="#"
                                class="btn btn-sm btn-outline-secondary edit-folder-btn"
                                data-id="{{ $folder->id }}"
                                data-name="{{ $folder->name }}"
                                data-action="{{ route('subscriber.folder.update', $folder->id) }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editFolderModal">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('subscriber.folder.destroy', $folder->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this folder?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2">No folders yet.</td>
                    </tr>
                    @endforelse

                    <tr>
                        <td colspan="2">
                            <a href="{{ route('subscriber.folders.shared') }}" class="d-flex align-items-start text-decoration-none">
                                <img src="{{ asset('frontend/assets/img/folder.png') }}" class="folder-icon me-2 mt-1" style="width: 40px; height: 40px;">

                                <div>
                                    <div class="fw-bold text-dark">Shared Folder</div>
                                    <div class="text-muted small">{{ $sharedDecisions }} decisions</div>
                                </div>
                            </a>


                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- New Folder Modal -->
<div class="modal fade" id="newFolderModal" tabindex="-1" aria-labelledby="newFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('subscriber.folder.create') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="name" class="form-control" placeholder="Folder Name" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Folder Modal -->
<div class="modal fade" id="editFolderModal" tabindex="-1" aria-labelledby="editFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="editFolderForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editFolderModalLabel">Rename</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="name" id="editFolderName" class="form-control" placeholder="Folder Name" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Folder</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-folder-btn');
        const editForm = document.getElementById('editFolderForm');
        const nameInput = document.getElementById('editFolderName');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const folderName = this.getAttribute('data-name');
                const formAction = this.getAttribute('data-action');

                nameInput.value = folderName;
                editForm.action = formAction;
            });
        });

        // Toggle view
        const gridViewBtn = document.getElementById('gridViewBtn');
        const listViewBtn = document.getElementById('listViewBtn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        gridViewBtn.addEventListener('click', function() {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
        });

        listViewBtn.addEventListener('click', function() {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
        });
    });

    function searchFolder(value) {
        const clearBtn = document.getElementById('clearFolderBtn');
        clearBtn.style.display = value.trim() !== '' ? 'inline-block' : 'none';

        const query = value.toLowerCase();

        // Grid View
        document.querySelectorAll('.folder-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });

        // List View
        document.querySelectorAll('.folder-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }


    function clearFolderSearch() {
        const input = document.getElementById('folderSearch');
        input.value = '';
        searchFolder('');
    }
</script>

@endpush