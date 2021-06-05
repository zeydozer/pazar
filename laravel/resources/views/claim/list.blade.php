@extends('index')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row page-title">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="float-right mt-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">İade</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="/iadeler/@yield('url')">@yield('name')</a>
                    </li>
                </ol>
            </nav>
            <h4 class="mb-1 mt-0">@yield('name')</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @yield('content-list')
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div>

@endsection

@section('style')

<!-- plugin css -->
<link href="/assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/jquery.fancybox.css" rel="stylesheet">
<link href="/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
<link href="/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/multiselect/multi-select.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />

<style>

    .dataTables_wrapper
    {
        overflow-x: scroll;
        padding-bottom: 1rem;
    }

    #custom-datatable td, 
    #order-products td,
    #custom-datatable th,
    #order-products th
    {
        vertical-align: middle;
    }

    #custom-datatable td
    {
        white-space: normal;
    }

    #custom-datatable .select2-container .select2-selection--single .select2-selection__rendered
    {
        padding-right: 28px;
    }

    #custom-datatable .input-group
    {
        width: 120px;
        flex-wrap: nowrap;
    }

    #custom-datatable .input-group .btn,
    #custom-datatable .input-group .form-control,
    #custom-datatable .input-group .select2,
    .select2-container
    {
        font-size: 8pt;
    }

    #custom-datatable .input-group .input-group-text
    {
        padding: .25rem .5rem;
        font-size: 12pt;
        line-height: 1;
    }

    #custom-datatable .input-group .select2-container .select2-selection--single
    {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    #custom-datatable .input-group .select2-container .select2-selection--single .select2-selection__rendered
    {
        line-height: 33px;
    }

    #custom-datatable .spinner-border
    {
        width: 19px;
        height: 19px;
        border-width: 2px;
        display: none;
    }

    #custom-datatable td.child .input-group
    {
        margin-top: .5rem;
    }

</style>

@yield('style-list')

@endsection

@section('script')

<!-- plugin js -->
<script src="/assets/js/jquery.fancybox.js"></script>
<script src="/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="/assets/libs/select2/select2.min.js"></script>
<script src="/assets/libs/multiselect/jquery.multi-select.js"></script>
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>

<!-- datatable js -->
<script src="/assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
<script src="/assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

<script src="/assets/libs/datatables/dataTables.buttons.min.js"></script>
<script src="/assets/libs/datatables/buttons.bootstrap4.min.js"></script>
<script src="/assets/libs/datatables/buttons.html5.min.js"></script>
<script src="/assets/libs/datatables/buttons.flash.min.js"></script>
<script src="/assets/libs/datatables/buttons.print.min.js"></script>

<script src="/assets/libs/datatables/dataTables.keyTable.min.js"></script>
<script src="/assets/libs/datatables/dataTables.select.min.js"></script>

<!-- Init js-->
<script src="/assets/js/pages/datatables.init.js"></script>
<script src="/assets/js/pages/form-advanced.init.js"></script>

@yield('script-list')

@endsection