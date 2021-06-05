@extends('index')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row page-title">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="float-right mt-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Ürün</a></li>
                    <li class="breadcrumb-item">
                        <a href="/urunler/@yield('url')">@yield('name')</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="/urun/@yield('url')">Kayıt</a>
                    </li>
                </ol>
            </nav>
            <h4 class="mb-1 mt-0">Kayıt <i class="uil uil-plus-circle ml-1"></i></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <form id="content" class="needs-validation" novalidate>
                        @yield('content-edit')
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
<link href="/assets/libs/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/summernote/summernote-bs4.css" rel="stylesheet" />
<link href="/assets/css/jquery.fancybox.css" rel="stylesheet">

<style>

    #content .form-control:disabled, 
    #content .form-control[readonly]
    {
        background-color: #f3f4f7;
    }

    .dropzone .dz-preview
    {
        width: 25%;
        margin: 0;
        padding-left: 7.5px;
        padding-right: 7.5px;
        margin-bottom: 15px;
    }

    .dropzone .dz-preview .dz-image
    {
        width: 100%;
    }

    #content img
    {
        max-width: unset;
        max-height: unset;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

</style>

@yield('style-edit')

@endsection

@section('script')

<!-- Plugins Js -->
<script src="/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="/assets/libs/select2/select2.min.js"></script>
<script src="/assets/libs/multiselect/jquery.multi-select.js"></script>
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
<script src="/assets/libs/dropzone/dropzone.min.js"></script>
<script src="/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="/assets/js/jquery.fancybox.js"></script>
<script src="/assets/js/jquery-ui.min.js"></script>

<!--Summernote js-->
<script src="/assets/libs/summernote/summernote-bs4.min.js"></script>

<!-- Validation init js-->
<script src="/assets/js/pages/form-validation.init.js"></script>

<!-- Init js-->
<script src="/assets/js/pages/form-advanced.init.js"></script>
<script src="/assets/js/pages/form-editor.init.js"></script>

<script type="text/javascript">
    
    Dropzone.prototype.defaultOptions.maxFilesize = 3,
    Dropzone.prototype.defaultOptions.acceptedFiles = 'image/*',
    Dropzone.prototype.defaultOptions.headers = {'X-CSRF-TOKEN': '{{ csrf_token() }}'};

    Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tarayıcınız drag'n'drop dosya yüklemelerini desteklemiyor.",
    Dropzone.prototype.defaultOptions.dictCancelUpload = "İptal et",
    Dropzone.prototype.defaultOptions.dictUploadCanceled = "İptal edildi.",
    Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "İptal etmek istediğinizden emin misiniz?",
    Dropzone.prototype.defaultOptions.dictRemoveFile = "Kaldır",
    Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Daha fazla dosya yükleyemezsiniz.",
    Dropzone.prototype.defaultOptions.dictFileTooBig = "Dosya en fazla "+ Dropzone.prototype.defaultOptions.maxFilesize +" MiB olmalı.",
    Dropzone.prototype.defaultOptions.dictInvalidFileType = "Dosya tipi resim olmalı.";
    
</script>

@yield('script-edit')

@endsection