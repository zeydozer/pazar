@extends('index')

@section('title', 'Ayar')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row page-title">
        <div class="col-md-12">
            <h4 class="mb-1 mt-0">Ayar</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form id="content" class="needs-validation" novalidate>
                        <div class="form-group">
                            <select data-plugin="customselect" class="form-control" name="id" 
                                onchange='location.href = "/ayar/"+ value'>
                                @foreach ($datas as $data)
                                
                                <?php $select = $data->id == $incoming->id ? 'selected' : ''; ?>
                                
                                <option value="{{ $data->id }}" {{ $select }}>{{ ucfirst($data->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        @foreach (json_decode($incoming->data) as $name => $value)
                        <div class="form-group">
                            <label class="text-capitalize">{{ str_replace('_', ' ', $name) }}</label>
                            <input type="text" class="form-control" name="data[{{ $name }}]" 
                                value="{{ $value }}">
                        </div>
                        @endforeach
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="active"
                                name="active" <?php if ($incoming->active) { ?> checked <?php } ?>>
                            <label class="custom-control-label" for="active">Aktif</label>
                        </div>
                        <hr>
                        <button class="btn btn-primary d-inline-flex align-items-center justify-content-center" type="submit">
                            <div class="spinner-grow text-white mr-2" role="status" style="display: none">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <span>Kaydet</span>
                        </button>
                    </form>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container-fluid -->

@endsection

@section('style')

<!-- Plugins css -->
<link href="/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
<link href="/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/multiselect/multi-select.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />

@endsection

@section('script')

<!-- Plugins Js -->
<script src="/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="/assets/libs/select2/select2.min.js"></script>
<script src="/assets/libs/multiselect/jquery.multi-select.js"></script>
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
<script src="/assets/libs/parsleyjs/parsley.min.js"></script>

<!-- Validation init js-->
<script src="/assets/js/pages/form-validation.init.js"></script>

<!-- Init js-->
<script src="/assets/js/pages/form-advanced.init.js"></script>

@endsection