@extends('index')

@section('title', 'Özellik')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row page-title">
        <div class="col-md-12">
            <h4 class="mb-1 mt-0">Özellik</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form id="content" class="needs-validation" novalidate>
                        <div class="form-group">
                            <select data-plugin="customselect" class="form-control" name="id" 
                                onchange='location.href = "/urun/ozellik/"+ value'>
                                <option value="" selected>+ Yeni</option>
                                @foreach ($datas as $data)
                                
                                <?php $select = $data->id == $incoming->id ? 'selected' : ''; ?>
                                
                                <option value="{{ $data->id }}" {{ $select }}>{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        @if ($categories)
                        <div class="form-group">
                            <label>Bağlı Kategori</label>
                            <select data-plugin="customselect" class="form-control" name="category_id">
                                <option value="" selected>- Yok</option>                                
                                @foreach ($categories as $id => $name)
                                
                                <?php $select = $id == $incoming->category_id ? 'selected' : '' ?>
                                
                                <option value="{{ $id }}" {{ $select }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group">
                            <label>İsim</label>
                            <input type="text" class="form-control" name="name" value="{{ $incoming->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Seçenekler</label>
                            <input type="text" class="form-control" name="option" value="{{ $incoming->option }}"
                                aria-describedby="option-help" placeholder="seçenek 1, seçenek 2, seçenek 3">
                            <small id="option-help" class="form-text text-muted">
                                Boş bırakılırsa özellik tipi "serbest", bırakılmazsa "seçenekli" olacaktır.
                            </small>
                        </div>
                        <div class="custom-control custom-checkbox mb-0">
                            <input type="checkbox" class="custom-control-input" id="require"
                                name="require" <?php if ($incoming->require) { ?> checked <?php } ?>>
                            <label class="custom-control-label" for="require">Zorunlu</label>
                        </div>
                        <hr>
                        <button class="btn btn-primary d-inline-flex align-items-center justify-content-center" type="submit">
                            <div class="spinner-grow text-white mr-2" role="status" style="display: none">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <span>Kaydet</span>
                        </button>
                        @if ($incoming->id)
                        <button class="btn btn-danger d-inline-flex align-items-center justify-content-center ml-2" type="submit">
                            <div class="spinner-grow text-white mr-2" role="status" style="display: none">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <span>Sil</span>
                        </button>
                        @endif
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