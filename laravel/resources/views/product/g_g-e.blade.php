@extends('product.edit')

@section('title', 'Ürün - Gittigidiyor - Kayıt')

@section('name', 'Gittigidiyor')

@section('url', 'gittigidiyor')

@section('content-edit')

@if (isset($incoming->product->photos->photo))

<?php 

if (!is_array($incoming->product->photos->photo))

    $incoming->product->photos->photo = [$incoming->product->photos->photo];

?>

@if (count($incoming->product->photos->photo) > 0)
<div class="row mb-3" id="photos">
    @foreach ($incoming->product->photos->photo as $i => $photo)
    <div class="col-lg-3 col-md-4">
        <a href="{{ $photo->url }}" data-fancybox="pro">
            <img src="{{ $photo->url }}">
        </a>
        <input type="hidden" name="photos[photo][{{ $i }}][url]" value="{{ $photo->url }}">
        <!-- <input type="hidden" name="photos[photo][{{ $i }}][order]" value="{{ $i + 1 }}"> -->
        <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="photo-del-{{ $i }}"
                name="photo-del[]" value="{{ $photo->url }}">
            <label class="custom-control-label" for="photo-del-{{ $i }}">Kaldır</label>
        </div>
    </div>
    @endforeach
</div>
@endif
@endif
<div class="form-group row">
    <div class="col-md-6">
        <label>Profil</label>
        <input type="file" class="form-control" name="profile">
    </div>
    <div class="col-md-6">
        <label>Fotoğraf</label>
        <input type="file" class="form-control" name="photo-add[]" multiple>
    </div>
</div>
<hr>
<div class="form-group" id="categories">
    <label>Kategori</label>
    <div class="input-group">
        <select data-plugin="customselect" class="form-control" name="category">
            <option value="" selected>- Yok</option>            
            @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group" id="catalogs" style="display: none">
    <label>Katalog *</label>
    <select data-plugin="customselect" class="form-control" name="catalogId" 
        data-placeholder="Arayın" data-select2-id="catalog">
        <option value="">- Yok</option>
    </select>
</div>
<div id="attribute"></div>
<hr class="mt-0">
<div class="form-group row">
    <div class="col-md-8">
        <label>Ürün Adı</label>
        <input type="text" class="form-control" name="product[title]" value="{{ $incoming->product->title }}" required>
    </div>
    <div class="col-md-4">
        <label>Stok Kodu</label>
        <input type="text" class="form-control" name="itemId" value="{{ $incoming->itemId }}" required>
    </div>
</div>
<div class="form-group">
    <label>Ön Açıklama</label>
    <input type="text" class="form-control" name="product[subtitle]" 
    value="@if (isset($incoming->product->subtitle)) {{ $incoming->product->subtitle }} @endif">
</div>
<div class="form-group">
    <label>Açıklama</label>
    <textarea id="summernote-editor" name="product[description]">
        
        <?php echo $incoming->product->description ?>
    
    </textarea>
</div>
<div class="form-group row">
    <div class="col-md-3">
        <label>Liste Fiyatı</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" name="product[marketPrice]" data-max="999999"
            value="@if (isset($incoming->product->marketPrice)) {{ $incoming->product->marketPrice }} @endif">
    </div>
    <div class="col-md-3">
        <label>Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" name="product[buyNowPrice]" required data-max="999999"
            value="{{ $incoming->product->buyNowPrice }}">
    </div>
    <div class="col-md-3">
        <label>Listele</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="Gün" data-step="1"
            data-decimals="0" name="product[listingDays]" required data-max="360"
            value="{{ $incoming->product->listingDays }}">
    </div>
    <div class="col-md-3">
        <label>Stok</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="Adet" data-step="1"
            data-decimals="0" name="product[productCount]" required data-max="999"
            value="{{ $incoming->product->productCount }}">
    </div>
</div>
<hr>
<button class="btn btn-primary d-inline-flex align-items-center justify-content-center" type="submit">
    <div class="spinner-grow text-white mr-2" role="status" style="display: none">
        <span class="sr-only">Yükleniyor...</span>
    </div>
    <span>Kaydet</span>
</button>
@if ($incoming->productId)
<button class="btn btn-danger d-inline-flex align-items-center justify-content-center ml-2" type="submit">
    <div class="spinner-grow text-white mr-2" role="status" style="display: none">
        <span class="sr-only">Yükleniyor...</span>
    </div>
    <span>Sil</span>
</button>
@endif


@endsection

@section('style-edit')

<style>

    #photos
    {
        margin-top: -.5rem;
    }

    #photos [class^="col"]
    {
        margin-top: .5rem;
    }

    #photos a
    {
        display: block;
    }

    #photos img
    {
        height: 200px;
    }

    #categories .input-group:not(:last-child)
    {
        margin-bottom: 1rem;
    }

    .stock:not(:first-child)
    {
        border-top: 1px solid rgba(0,0,0,.1);
        padding-top: 1rem;
    }

    #content .form-control[readonly].date-picker
    {
        background-color: #fff;
    }

</style>

@endsection

@section('script-edit')

<script>

    $('[name="category"]').attr('name', null).addClass('select-gittigidiyor');

    $('#content').append($('<input>').attr(
    {
        type: 'hidden',
        name: 'product[categoryCode]',
        value: '{{ $incoming->product->categoryCode }}'
    }));

    attributes('{{ $incoming->product->categoryCode }}');

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

            else
            {
                $('[name="product[categoryCode]"]').val(_this.val());

                attributes(_this.val());
            }
        });
    });

    function attributes(value)
    {
        var datas = 
        {
            type: 'gittigidiyor', 
            task: 'attribute', 
            id: value,
            incoming: '{{ $incoming->productId }}'
        };

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                $('#attribute').html(resp);

                $('#attribute select').select2();

                $('#attribute').find('[required]').each(function()
                {
                    var label = $(this).closest('.col-md-4').find('label');

                    label.html(label.html() +' *');
                });
            }

            else $('#attribute').html(null);
        });

        datas.task = 'catalog-c';

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                $('#catalogs').show();

                $('[name="catalogId"]').attr('required', true).select2();

                setTimeout(function()
                {
                    $('#attribute').find('.col-md-4').each(function()
                    {
                        var text = $(this).find('label').html().replace(' *', '');

                            control = false;

                        $.each(resp, function(i, spec)
                        {
                            if (spec.name == text)

                                control = true;
                        });

                        if (!control)
                        {
                            $(this).find('label').html(text);

                            $(this).find('[required]').removeAttr('required').select2();
                        }
                    });

                }, 2000);
            }
            
            else
            {
                $('#catalogs').hide();

                $('[name="catalogId"]').removeAttr('required').val(null);
            }
        });
    }

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

    $('body').on('keyup', '[aria-controls^="select2-catalogId"]', delay(function()
    {
        var datas = 
        {
            type: 'gittigidiyor', 
            task: 'catalog', 
            name: $(this).val(),
            cat: $('[name="product[categoryCode]"]').val()
        };

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                var option = new Option('- Yok', '', true, true);

                $('[name="catalogId"]').html(null).append(option);

                $.each(resp, function(i, cat)
                {
                    option = new Option(cat.catalogName, cat.catalogAttributeId, true, true);

                    $('[name="catalogId"]').append(option).trigger('change')
                        .select2('close').select2('open').val(null);
                });
            }
        });
    
    }, 500));

</script>

@endsection