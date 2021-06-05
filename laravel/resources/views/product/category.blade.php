@extends('index')

@section('title', 'Kategori')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row page-title">
        <div class="col-md-12">
            <h4 class="mb-1 mt-0">Kategori</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form id="content" class="needs-validation" novalidate>
                        <div class="form-group">
                            <select data-plugin="customselect" class="form-control" name="id" 
                                onchange='location.href = "/urun/kategori/"+ value'>
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
                            <label>Bağlı Kategori</label>
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
                        <div class="form-group">
                            <label>İsim</label>
                            <input type="text" class="form-control" name="name" value="{{ $incoming->name }}" required>
                        </div>
                        <hr>
                        <div class="form-group row">
                            @foreach ($match as $name => $value)
                            <div class="col-md-6 mt-3 @if (!isset($categories[$name])) d-none @endif" id="match-categories-{{ $name }}">
                                <label>{{ ucfirst($name) }} Kategorisi</label>
                                <div class="input-group">
                                    <select data-plugin="customselect" class="form-control" name="match[{{ $name }}]">
                                        <option value="" selected>- Yok</option>
                                        
                                        @if (isset($categories[$name]))
                                        @foreach ($categories[$name] as $category)

                                        <?php $select = $category->id == $value ? 'selected' : '' ?>
                                        
                                        <option value="{{ $category->id }}" {{ $select }}>{{ $category->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="custom-control custom-checkbox mb-0">
                            <input type="checkbox" class="custom-control-input" id="root"
                                name="root" <?php if ($incoming->root) { ?> checked <?php } ?>>
                            <label class="custom-control-label" for="root">
                                Üst kategori komisyonu geçerli olsun.
                            </label>
                        </div>
                        <div class="form-group row">
                            @foreach ($commision as $name => $value)
                            <div class="col-md-6 mt-3 @if (!$temps[$name]) d-none @endif">
                                <label>{{ ucfirst($name) }} Komisyonu</label>
                                <input data-toggle="touchspin" type="text" data-bts-prefix="%" data-step="1.00"
                                    data-decimals="2" value="{{ $value }}" name="commision[{{ $name }}]" 
                                    required>
                            </div>
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

<style>

    [id^="match-categories"] .input-group:not(:last-child)
    {
        margin-bottom: 1rem;
    }

</style>

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

<script>

    $('[name="match[n11]"]').attr('name', null).addClass('select-n11');

    $('#content').append($('<input>').attr(
    {
        type: 'hidden',
        name: 'match[n11]',
        value: null
    }));

    $('body').on('change', '.select-n11', function()
    {
        var datas = 
        {
            type: 'n11',
            task: 'category',
            id: $(this).val(),
        };

            _this = $(this);

            container = '#'+ _this.closest('.form-group').attr('id');

            order = $('.select-n11').index(this);

        $('.select-n11').each(function()
        {
            var index = $('.select-n11').index(this);

            if (index > order)

                $(this).closest('.input-group').remove();
        });

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                var group = '<div class="input-group">';

                group += '<select class="form-control select-n11">';

                group += '<option value="">- Yok</option>';

                $.each(resp, function(i, category)
                {
                    group += '<option value="'+ category.id +'">'+ category.name +'</option>';
                });

                group += '</select></div>';

                $(group).insertAfter(container +' .input-group:eq('+ order +')');

                $('.select-n11').select2();
            }

            else $('[name="match[n11]"]').val(datas.id);
        });
    });

    @if (isset($match->n11))
        
        var datas = 
        {
            type: 'n11',
            task: 'category-parent',
            id: '{{ $match->n11 }}',
        };

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                var time = 0;

                $.each(resp, function(i, id)
                {
                    setTimeout(function()
                    {
                        $('.select-n11').eq(i).val(id).trigger('change');

                    }, time);

                    time += 1000;
                });

                setTimeout(function()
                {
                    $('.select-n11').eq(resp.length).val('{{ $match->n11 }}').trigger('change');

                    $('[name="match[n11]"]').val('{{ $match->n11 }}');

                }, time);
            }
        });

    @endif

    $('[name="match[gittigidiyor]"]').attr('name', null).addClass('select-gittigidiyor');

    $('#content').append($('<input>').attr(
    {
        type: 'hidden',
        name: 'match[gittigidiyor]',
        value: null
    }));

    $('body').on('change', '.select-gittigidiyor', function()
    {
        var datas = 
        {
            type: 'gittigidiyor',
            task: 'category',
            id: $(this).val(),
        };

            _this = $(this);

            container = '#'+ _this.closest('.form-group').attr('id');

            order = $('.select-gittigidiyor').index(this);

            ids = [];

        $('.select-gittigidiyor').each(function(i)
        {
            var index = $('.select-gittigidiyor').index(this);

            if (index > order)

                $(this).closest('.input-group').remove();

            else ids[i] = $(this).val();
        });

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                var group = '<div class="input-group">';

                group += '<select class="form-control select-gittigidiyor">';

                group += '<option value="">- Yok</option>';

                $.each(resp, function(i, category)
                {
                    group += '<option value="'+ category.categoryCode +'">'+ category.categoryName +'</option>';
                });

                group += '</select></div>';

                $(group).insertAfter(container +' .input-group:eq('+ order +')');

                $('.select-gittigidiyor').select2();
            }

            else $('[name="match[gittigidiyor]"]').val(ids.join(', '));
        });
    });

    @if (isset($match->gittigidiyor))
        
        var ids = '{{ $match->gittigidiyor }}'.split(', '), time = 0;

        $.each(ids, function(i, id)
        {
            setTimeout(function()
            {
                $('.select-gittigidiyor').eq(i).val(id).trigger('change');

            }, time);

            time += 1000;
        });

        $('[name="match[gittigidiyor]"]').val('{{ $match->gittigidiyor }}');

    @endif

</script>

@endsection