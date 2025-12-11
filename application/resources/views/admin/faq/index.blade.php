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
            <h3 class="mb-0">FAQ List</h3>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">
                FAQ List
              </li>
            </ol>
          </div>
          <div class="col-sm-6">
            <div class="float-sm-end">
              <a href="javascript:void()" onclick="permissions('faqs','1');" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Approved</a>
              <a href="javascript:void()" onclick="permissions('faqs','0');" class="btn btn-warning btn-sm"><i class="fa fa-times"></i> Disapproved</a>
              <a href="javascript:void()" onclick="deletedata('masterdelete','faqs');" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Multiple Delete</a>
              <a href="{{ route('faq.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Create New faq</a>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-4">
              <div class="card-body">
                <form id="form_check">
                  <table id="responsive-datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th width="4%"><input name="checkbox" onclick='checkedAll();' type="checkbox" readonly="readonly" /></th>
                        <th width="25%">Topic</th>
                        <th width="25%">Question</th>
                        <th width="10%">Sequence</th>
                        <th width="10%">Status</th>
                        <th width="20%" align="right">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($allfaq as $faq)
                      <tr id="tablerow{{ $faq->id }}" class="tablerow">
                        <td><input type="checkbox" name="summe_code[]" id="summe_code{{ $faq->id }}" value="{{ $faq->id }}" /></td>
                        <td>{{ $faq->topic }}</td>
                        <td>{{ $faq->question }}</td>
                        <td>{{ $faq->sequence }}</td>
                        <td>
                          @if($faq->status == 1)
                          <span style="background:#006600; padding:3px 8px; border-radius:5px; color: white;"><i class="fa fa-check"></i></span>
                          @else
                          <span style="background:#D91021; padding:3px 8px; border-radius:5px; color: white;"><i class="fa fa-times"></i></span>
                          @endif
                        </td>
                        <td align="center">
                          <a href="{{ route('faq.edit', $faq->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                          <button type="button" class="btn btn-danger btn-sm"
                            onclick="deleteSingle('<?php echo $faq->id; ?>','masterdelete','faqs')"><i class="fa fa-trash"></i></button>
                      </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>

                </form>
              </div>
              <div class="card-footer clearfix">
                {{ $allfaq->links() }}
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </main>
</div>
@endsection