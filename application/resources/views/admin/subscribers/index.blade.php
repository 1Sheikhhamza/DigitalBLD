@extends('admin.layouts.app')
@section('title', 'Subscriber List')
@section('content')
    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Subscription</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <a href="{{ route('subscribers.export', request()->all()) }}"
                                    class="btn btn-primary btn-sm m-2"><i class="bi bi-file-earmark-spreadsheet"></i>
                                    Download CSV</a>
                                <button type="button" onclick="permissions('subscribers','1');" style="color:#000; "
                                    class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                                <button type="button" onclick="permissions('subscribers','0');" style="color:#000; "
                                    class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                                <button type="button" onclick="deletedata('masterdelete','subscribers');"
                                    style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i>
                                    Multiple Delete</button>
                                <a href="{{ route('subscribers.create') }}" style="color:#fff; margin-right:20px"
                                    class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <style>
                        .autocomplete-suggestions {
                            border: 1px solid #ddd;
                            background: #fff;
                            overflow-y: auto;
                            max-height: 200px;
                            position: absolute;
                            z-index: 1000;
                            width: 90%;
                            border-radius: 0 0 5px 5px;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                            display: none;
                        }

                        .autocomplete-suggestion {
                            padding: 8px 12px;
                            cursor: pointer;
                        }

                        .autocomplete-suggestion:hover {
                            background-color: #f0f0f0;
                        }
                    </style>

                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('subscribers.index') }}" method="GET" class="row g-3">
                                <div class="col-md-3 position-relative">
                                    <input type="text" name="name" id="search-name" class="form-control" placeholder="Name"
                                        value="{{ request('name') }}" autocomplete="off">
                                    <div id="suggestions-name" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="col-md-3 position-relative">
                                    <input type="text" name="email" id="search-email" class="form-control"
                                        placeholder="Email" value="{{ request('email') }}" autocomplete="off">
                                    <div id="suggestions-email" class="autocomplete-suggestions"></div>
                                </div>
                                <!-- Mobile field: changed name from 'mobile' to 'phone' if that matches DB, but earlier code used 'mobile'.Sticking to 'mobile' logic but placeholder says Phone Number -->
                                <div class="col-md-3 position-relative">
                                    <input type="text" name="mobile" id="search-mobile" class="form-control"
                                        placeholder="Phone Number" value="{{ request('mobile') }}" autocomplete="off">
                                    <div id="suggestions-mobile" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date" class="form-control" placeholder="Registered Date"
                                        value="{{ request('date') }}">
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary w-100"><i
                                            class="bi bi-search"></i></button>
                                    <a href="{{ route('subscribers.index') }}" class="btn btn-secondary w-100 mt-1"><i
                                            class="bi bi-x"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @push('scripts')
                        <script>
                            $(document).ready(function () {
                                function setupAutocomplete(inputId, suggestionsId, field) {
                                    let timeout = null;
                                    const input = $('#' + inputId);
                                    const list = $('#' + suggestionsId);

                                    input.on('input', function () {
                                        const term = $(this).val();
                                        if (timeout) clearTimeout(timeout);
                                        if (term.length < 2) {
                                            list.hide();
                                            return;
                                        }

                                        timeout = setTimeout(() => {
                                            $.ajax({
                                                url: "{{ route('subscribers.suggestions') }}",
                                                data: { field: field, term: term },
                                                success: function (data) {
                                                    list.empty();
                                                    if (data.length > 0) {
                                                        data.forEach(item => {
                                                            const div = $('<div>')
                                                                .addClass('autocomplete-suggestion')
                                                                .text(item)
                                                                .on('click', function () {
                                                                    input.val(item);
                                                                    list.hide();
                                                                });
                                                            list.append(div);
                                                        });
                                                        list.show();
                                                    } else {
                                                        list.hide();
                                                    }
                                                }
                                            });
                                        }, 300);
                                    });

                                    $(document).on('click', function (e) {
                                        if (!$(e.target).closest('#' + inputId + ', #' + suggestionsId).length) {
                                            list.hide();
                                        }
                                    });
                                }

                                setupAutocomplete('search-name', 'suggestions-name', 'name');
                                setupAutocomplete('search-email', 'suggestions-email', 'email');
                                setupAutocomplete('search-mobile', 'suggestions-mobile', 'mobile');
                            });
                        </script>
                    @endpush

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Subscription List</h3>
                            <div class="card-tools"> <button type="button" class="btn btn-tool"
                                    data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i
                                        data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button"
                                    class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <form id="form_check">
                                    <table class="table m-0">
                                        <thead>
                                            <tr>
                                                <th width="4%">
                                                    <input type="checkbox" onclick="checkedAll();" readonly />
                                                </th>
                                                <th>SI</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Registered Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($subscribers as $subscriber)
                                                <tr id="tablerow{{ $subscriber->id }}" class="tablerow">
                                                    <td>
                                                        <input type="checkbox" name="summe_code[]"
                                                            id="summe_code{{ $subscriber->id }}" value="{{ $subscriber->id }}">
                                                    </td>
                                                    <td>{{ ($subscribers->currentPage() - 1) * $subscribers->perPage() + $loop->iteration }}
                                                    </td>
                                                    <td>{{ $subscriber->name }}</td>
                                                    <td>{{ $subscriber->email }}</td>
                                                    <td>{{ $subscriber->mobile }}</td>
                                                    <td>
                                                        {{ $subscriber->created_at->format('d M, Y h:i A') }}
                                                    </td>
                                                    <td>
                                                        @if($subscriber->status ?? true)
                                                            <span class="badge text-bg-success">Active</span>
                                                        @else
                                                            <span class="badge text-bg-danger">Inactive</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('subscribers.edit', $subscriber->id) }}" title="Edit"
                                                            class="btn btn-warning btn-sm me-1">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('subscribers.show', $subscriber->id) }}" title="View"
                                                            class="btn btn-info btn-sm me-1">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" title="Delete"
                                                            onclick="deleteSingle('{{ $subscriber->id }}', 'masterdelete', 'subscribers')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </form>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $subscribers->links('pagination::bootstrap-4')}}
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>

@endsection