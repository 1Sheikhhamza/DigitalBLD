@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="mb-0">Legal Decision</h3>
                    </div>
                    <div class="col-sm-8">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('ocr_extractions','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('ocr_extractions','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','ocr_extractions');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('ocr_extractions.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New (Manual Legal Decision)</a>
                        </ol>
                    </div>
                </div>

                <form method="GET" action="{{ route('ocr_extractions.index') }}" class="mb-3 mt-3">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Case No</label>
                            <input type="text" name="case_no" value="{{ request('case_no') }}" class="form-control" placeholder="Case No">
                        </div>
                        <div class="col-md-2">
                            <label>Parties Name</label>
                            <input type="text" name="parties" value="{{ request('parties') }}" class="form-control" placeholder="Parties Name">
                        </div>
                        <div class="col-md-2">
                        <label>Division</label>
                            <select name="division" class="form-control">
                                <option value="">Select Division</option>
                                <option value="Appellate Division" {{ request('division') == 'Appellate Division' ? 'selected' : '' }}>Appellate Division</option>
                                <option value="High Court Division" {{ request('division') == 'High Court Division' ? 'selected' : '' }}>High Court Division</option>
                                <!-- Add more divisions -->
                            </select>
                        </div>
                        <div class="col-md-2">
                        <label>Volume</label>
                            <select name="volume_id" class="form-control select2Data">
                                <option value="">Select Volume</option>
                                @foreach($volumeList as $id => $volumeNumber)
                                <option value="{{ $id }}" {{ request('volume_id') == $id ? 'selected' : '' }}>
                                    {{ $volumeNumber }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Publication Year</label>
                            <input type="number" name="published_year" value="{{ request('published_year') }}" class="form-control" placeholder="Published Year">
                        </div>
                        <div class="col-md-2">
                            <label>Judge Name</label>
                            <input type="text" name="judgename" value="{{ request('judgename') }}" class="form-control" placeholder="Judge Name">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            <a href="{{ route('ocr_extractions.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th width="4%"><input name="checkbox" onclick='checkedAll();' type="checkbox" readonly="readonly" /></th>
                                            <th>SI</th>
                                            <th>Volume</th>
                                            <th>Case Number</th>
                                            <th>Division</th>
                                            <th>Parties Name</th>
                                            <th>Date of Judgment</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 0 @endphp
                                        @foreach($ocr_extractions as $key => $ocr_extraction)
                                        @php $i++ @endphp
                                        <tr id="tablerow<?php echo $ocr_extraction->id; ?>" class="tablerow">
                                            <td><input type="checkbox" name="summe_code[]" id="summe_code<?php echo $ocr_extraction->id; ?>" value="{{ $ocr_extraction->id }}" /></td>
                                            <td>{{ $ocr_extractions->firstItem() + $key }}</td>
                                            <td>{{ $ocr_extraction->volume ? $ocr_extraction->volume->number : '' }}</td>
                                            <td>{{ $ocr_extraction->case_no }}</td>
                                            <td>{{ $ocr_extraction->division }}</td>
                                            <td>{!! $ocr_extraction->parties !!}</td>
                                            <td>{{ $ocr_extraction->decided_on }}</td>
                                            <td>
                                                {!! $ocr_extraction->status ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                            </td>
                                            <td>
                                                <a href="{{ route('ocr_extractions.edit', $ocr_extraction->id) }}" title="Edit Record" class="btn btn-warning btn-sm me-2"><i class="fa fa-edit"></i></a>
                                                <a href="{{ route('ocr_extractions.show', $ocr_extraction->id) }}" title="View Details" class="btn btn-info btn-sm me-2"><i class="fa fa-eye"></i></a>
                                                <!-- <button type="button" class="btn btn-danger btn-sm" title="Delete Record"
                                                    onclick="deleteSingle('<?php echo $ocr_extraction->id; ?>','masterdelete','ocr_extractions')"><i class="fa fa-trash"></i></button> -->
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $ocr_extractions->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection