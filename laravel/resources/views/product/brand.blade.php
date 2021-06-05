@extends('index')

@section('title', 'Marka')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row page-title">
        <div class="col-md-12">
            <h4 class="mb-1 mt-0">Marka</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form id="content" class="needs-validation" novalidate>
                        <div class="form-group">
                            <select data-plugin="customselect" class="form-control" name="id" 
                                onchange='location.href = "/urun/marka/"+ value'>
                                <option value="" selected>+ Yeni</option>
                                @if ($datas)
                                @foreach ($datas as $id => $name)
                                
                                <?php $select = $id == $incoming->id ? 'selected' : ''; ?>
                                
                                <option value="{{ $id }}" {{ $select }}>{{ $name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <hr>
                        @if ($datas)
                        <div class="form-group">
                            <label>Bağlı Marka</label>
                            <select data-plugin="customselect" class="form-control" name="root_id">
                                <option value="" selected>- Yok</option>
                                
                                <?php

                                foreach ($datas as $id => $name):
                                
                                    if ($incoming->id == $id)
                                    
                                        continue;

                                    $select = $id == $incoming->root_id ? 'selected' : ''; 
                                
                                ?>
                                
                                <option value="{{ $id }}" {{ $select }}>{{ $name }}</option>
                                
                                <?php endforeach ?>

                            </select>
                        </div>
                        @endif
                        @if ($incoming->photo)
                        <div class="mb-3">
                            <a href="/assets/images/brands/{{ $incoming->photo }}" 
                                data-fancybox class="d-block">
                                <img src="/assets/images/brands/{{ $incoming->photo }}">
                            </a>
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" id="photo-del"
                                    name="photo-del" value="{{ $incoming->photo }}">
                                <label class="custom-control-label" for="photo-del">Kaldır</label>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Fotoğraf</label>
                            <input type="file" class="form-control" name="photo">
                        </div>
                        <div class="form-group">
                            <label>İsim</label>
                            <input type="text" class="form-control" name="name" value="{{ $incoming->name }}" required>
                        </div>
                        <hr class="mb-0">
                        <div class="form-group row">
                            @foreach ($match as $name => $value)
                            <div class="col-md-6 mt-3 @if (!isset($brands[$name])) d-none @endif">
                                <label>{{ ucfirst($name) }} Markası</label>
                                <select data-plugin="customselect" class="form-control" name="match[{{ $name }}]"
                                    data-placeholder="Arayın" data-select2-id="{{ $name }}-brand">
                                    <option value="" selected>- Yok</option>
                                    
                                    @if (isset($brands[$name]))
                                    @foreach ($brands[$name] as $brand)

                                    <?php $select = $brand->id == $value ? 'selected' : '' ?>
                                    
                                    <option value="{{ $brand->id }}" {{ $select }}>{{ $brand->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <input type="hidden" name="match_name[{{ $name }}]" value="{{ $match_name[$name] }}">
                            @endforeach
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
<link href="/assets/css/jquery.fancybox.css" rel="stylesheet">

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
<script src="/assets/js/jquery.fancybox.js"></script>

<!-- Validation init js-->
<script src="/assets/js/pages/form-validation.init.js"></script>

<!-- Init js-->
<script src="/assets/js/pages/form-advanced.init.js"></script>

<script>

    function delay(callback, ms) 
    {
        var timer = 0;
        
        return function() 
        {
            var context = this, args = arguments;
            
            clearTimeout(timer);
            
            timer = setTimeout(function () 
            {
              callback.apply(context, args);
            
            }, ms || 0);
        };
    }

    $('body').on('keyup', '[aria-controls^="select2-matchtrendyol"]', delay(function()
    {
        var datas = {type: 'trendyol', task: 'brand', name: $(this).val()};

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                var option = new Option('- Yok', '', true, true);

                $('[name="match[trendyol]"]').html(null).append(option);

                $.each(resp, function(i, brand)
                {
                    option = new Option(brand.name, brand.id, true, true);

                    $('[name="match[trendyol]"]').append(option).trigger('change')
                        .select2('close').select2('open').val(null);
                });
            }
        });
    
    }, 500));

    @if ($match['trendyol'])
        var datas = {type: 'trendyol', task: 'brand', name: '{{ $match_name["trendyol"] }}'.split(' ')[0]};

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                var option = new Option('- Yok', '', true, true);

                $('[name="match[trendyol]"]').html(null).append(option);

                $.each(resp, function(i, brand)
                {
                    var option = new Option(brand.name, brand.id, true, true);

                    $('[name="match[trendyol]"]').append(option).trigger('change');
                });

                $('[name="match[trendyol]"]').val('{{ $match["trendyol"] }}').trigger('change');
            }
        });
    @endif

    $('[name="match[trendyol]"]').on('select2:select', function(e) 
    {
        var data = e.params.data;

            name = data['_resultId'].split('-')[1].replace('match', '');
        
        $('[name="match_name['+ name +']"]').val(data['text']);
    });

</script>

@endsection