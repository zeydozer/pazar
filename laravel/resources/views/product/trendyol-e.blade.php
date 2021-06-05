@extends('product.edit')

@section('title', 'Ürün - Trendyol - Kayıt')

@section('name', 'Trendyol')

@section('url', 'trendyol')

@section('content-edit')

@if (count($incoming->images) > 0)
<div class="row mb-3" id="photos">
    @foreach ($incoming->images as $i => $photo)

    <?php
    
    if (strpos($photo->url, 'cdn.dsmcdn.com') !== false && strpos($photo->url, '_org_zoom') === false)

        $photo->url = str_replace(['.jpg', '.jpeg'], ['_org_zoom.jpg', '_org_zoom.jpeg'], $photo->url);

    ?>

    <div class="col-lg-3 col-md-4">
        <a href="{{ $photo->url }}" data-fancybox="pro">
            <img src="{{ $photo->url }}">
        </a>
        <input type="hidden" name="photos[]" value="{{ $photo->url }}">
        <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="photo-del-{{ $i }}"
                name="photo-del[]" value="{{ $photo->url }}">
            <label class="custom-control-label" for="photo-del-{{ $i }}">Kaldır</label>
        </div>
    </div>
    @endforeach
</div>
@endif
<div class="form-group">
    <label>Fotoğraf</label>
    <input type="file" class="form-control" name="photo-add[]" multiple>
</div>
<div class="form-group row">
    <div class="col-md-8">
        <label>Ürün Adı</label>
        <input type="text" class="form-control" name="title" value="{{ $incoming->title }}" required>
    </div>
    <div class="col-md-4">
        <label>Barkod</label>
        <input type="text" class="form-control" name="barcode" value="{{ $incoming->barcode }}" 
            required <?php if ($incoming->barcode) { ?> disabled <?php } ?>>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label>Stok Kodu</label>
        <input type="text" class="form-control" name="stockCode" value="{{ $incoming->stockCode }}" required>
    </div>
    <div class="col-md-4">
        <label>Model Kodu</label>
        <input type="text" class="form-control" name="productMainId" value="{{ $incoming->productMainId }}" 
            <?php if (isset($incoming->approved) && $incoming->approved === true) { ?> disabled <?php } ?>
            required>
    </div>
    <div class="col-md-4">
        <label>Marka</label>
        <select data-plugin="customselect" class="form-control" name="brandId" data-placeholder="Arayın"
            <?php if (isset($incoming->approved) && $incoming->approved === true) { ?> disabled <?php } ?>
            data-select2-id="brand" required>
            <option value="">- Yok</option>
        </select>
    </div>
</div>
<hr>
<div class="form-group row">
    <div class="col-md-12">
        <label>Kategori</label>
        <select data-plugin="customselect" class="form-control" name="categoryId" data-placeholder="Arayın"
            <?php if ($incoming->pimCategoryId) { ?> disabled <?php } ?> data-select2-id="category" required>
            <option value="">- Yok</option>
            @foreach ($categories as $id => $name)
            
            <?php $selected = $incoming->pimCategoryId == $id ? 'selected' : '' ?>
            
            <option value="{{ $id }}" {{ $selected }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div id="attribute">
    @include('product.attribute-l', 
    [
        'type' => 'trendyol', 
        'attributes' => isset($attributes) ? $attributes : [],
    ])
</div>
<hr class="mt-0">
<div class="form-group">
    <label>Açıklama</label>
    <textarea id="summernote-editor" name="description">
        
        <?php echo $incoming->description ?>
    
    </textarea>
</div>
@if (!$incoming->barcode)
<div class="form-group row">
    <div class="col-md-6">
        <label>Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" name="listPrice" required value="{{ $incoming->listPrice }}">
    </div>
    <div class="col-md-6">
        <label>İndirimli Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" name="salePrice" required value="{{ $incoming->salePrice }}">
    </div>
    <input type="hidden" name="currentType" value="TRY">
</div>
@endif
<div class="form-group row">
    <div class="col-md-4">
        <label>Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" value="{{ $incoming->listPrice }}" name="listPrice" 
            data-max="99999999" required>
    </div>
    <div class="col-md-4">
        <label>İndirimli Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" value="{{ $incoming->salePrice }}" name="salePrice" 
            data-max="99999999" required>
    </div>
    <div class="col-md-4">
        <label>Stok</label>
        <input data-toggle="touchspin" type="text" name="quantity" data-max="99999999"
            value="{{ $incoming->quantity }}" required>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label>Kargo</label>
        <select data-plugin="customselect" class="form-control" name="cargoCompanyId" 
            data-placeholder="Arayın" data-select2-id="cargo" required>
            <option value="">- Yok</option>
            @foreach ($cargos as $cargo)
            <option value="{{ $cargo->id }}">{{ $cargo->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Desi</label>
        <input data-toggle="touchspin" type="text" data-step="0.01" data-decimals="2" 
            value="{{ $incoming->dimensionalWeight }}" name="dimensionalWeight" 
            required>
    </div>
    <div class="col-md-4">
        <label>Kdv</label>
        <input data-toggle="touchspin" type="text" data-bts-pretfix="%" data-step="1" 
            data-decimals="0" value="{{ $incoming->vatRate }}" name="vatRate"
            required>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <label>Sevkiyat Adresi</label>
        <select data-plugin="customselect" class="form-control" name="shipmentAddressId" 
            data-placeholder="Arayın" data-select2-id="shipment">
            <option value="">- Yok</option>
            
            <?php 

            if (isset($addresses->supplierAddresses)) :

                foreach ($addresses->supplierAddresses as $address) :
                
                    if ($address->addressType != 'Shipment')

                        continue;
            
            ?>
            
            <option value="{{ $address->id }}">{{ $address->fullAddress }}</option>            
            
            <?php endforeach; endif; ?>

        </select>
    </div>
    <div class="col-md-6">
        <label>İade Adresi</label>
        <select data-plugin="customselect" class="form-control" name="returningAddressId" 
            data-placeholder="Arayın" data-select2-id="return">
            <option value="">- Yok</option>
            
            <?php 

            if (isset($addresses->supplierAddresses)) :

                foreach ($addresses->supplierAddresses as $address) :
                
                    if ($address->addressType != 'Returning')

                        continue;
            
            ?>
            
            <option value="{{ $address->id }}">{{ $address->fullAddress }}</option>            
            
            <?php endforeach; endif; ?>

        </select>
    </div>
</div>
<hr>
<button class="btn btn-primary d-inline-flex align-items-center justify-content-center" type="submit">
    <div class="spinner-grow text-white mr-2" role="status" style="display: none">
        <span class="sr-only">Yükleniyor...</span>
    </div>
    <span>Kaydet</span>
</button>

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

</style>

@endsection

@section('script-edit')

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

    $('body').on('keyup', '[aria-controls^="select2-brandId"]', delay(function()
    {
        var datas = {type: 'trendyol', task: 'brand', name: $(this).val()};

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                var option = new Option('- Yok', '', true, true);

                $('[name="brandId"]').html(null).append(option);

                $.each(resp, function(i, brand)
                {
                    option = new Option(brand.name, brand.id, true, true);

                    $('[name="brandId"]').append(option).trigger('change')
                        .select2('close').select2('open').val(null);
                });
            }
        });
    
    }, 500));

    @if ($incoming->brand)
        var datas = {type: 'trendyol', task: 'brand', name: '{{ $incoming->brand }}'.split(' ')[0]};

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                var option = new Option('- Yok', '', true, true);

                $('[name="brandId"]').html(null).append(option);

                $.each(resp, function(i, brand)
                {
                    option = new Option(brand.name, brand.id, true, true);

                    $('[name="brandId"]').append(option).trigger('change');
                });

                $('[name="brandId"]').val('{{ $incoming->brandId }}').trigger('change');
            }
        });
    @endif

    $('[name="categoryId"]').change(function()
    {
        var datas = 
        {
            type: 'trendyol', 
            task: 'attribute', 
            id: $(this).val()
        };

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                $('#attribute').html(resp);

                $('#attribute select').select2();

                $('#attribute').find('[required]').each(function()
                {
                    var index = $(this).closest('.form-group').find('.form-control').index(this);

                        label = $(this).closest('.form-group').find('label').eq(index);

                    label.html(label.html() +' *');
                });
            }

            else $('#attribute').html(null);
        });
    });

</script>

@endsection