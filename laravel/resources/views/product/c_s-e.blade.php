@extends('product.edit')

@section('title', 'Ürün - Çiçeksepeti - Kayıt')

@section('name', 'Çiçeksepeti')

@section('url', 'ciceksepeti')

@section('content-edit')

@if (count($incoming->images) > 0)
<div class="row mb-3" id="photos">
    @foreach ($incoming->images as $i => $photo)
    <div class="col-lg-3 col-md-4">
        <a href="{{ $photo }}" data-fancybox="pro">
            <img src="{{ $photo }}">
        </a>
        <input type="hidden" name="photos[]" value="{{ $photo }}">
        <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="photo-del-{{ $i }}"
                name="photo-del[]" value="{{ $photo }}">
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
        <input type="text" class="form-control" name="productName" value="{{ $incoming->productName }}" required>
    </div>
    <div class="col-md-4">
        <label>Barkod</label>
        <input type="text" class="form-control" name="barcode" value="{{ $incoming->barcode }}">
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label>Stok Kodu</label>
        <input type="text" class="form-control" name="stockCode" value="{{ $incoming->stockCode }}" required>
    </div>
    <div class="col-md-4">
        <label>Model Kodu</label>
        <input type="text" class="form-control" name="mainProductCode" value="{{ $incoming->mainProductCode }}" required>
    </div>
    <div class="col-md-4">
        <label>Kategori</label>
        <select data-plugin="customselect" class="form-control" name="categoryId" 
        data-placeholder="Arayın" data-select2-id="category" required>
            <option value="">- Yok</option>
            @foreach ($categories as $id => $name)
            
            <?php $selected = $incoming->categoryId == $id ? 'selected' : '' ?>
            
            <option value="{{ $id }}" {{ $selected }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>
<hr>
<div id="attribute">
    @include('product.attribute-l', 
    [
        'type' => 'ciceksepeti', 
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
<div class="form-group row">
    <div class="col-md-4">
        <label>Liste Fiyatı</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" value="{{ $incoming->listPrice }}" name="listPrice" 
            data-max="99999999">
    </div>
    <div class="col-md-4">
        <label>Satış Fiyatı</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" value="{{ $incoming->salesPrice }}" name="salesPrice" 
            data-max="99999999" required>
    </div>
    <div class="col-md-4">
        <label>Stok</label>
        <input data-toggle="touchspin" type="text" name="stockQuantity" data-max="99999999"
            value="{{ $incoming->stockQuantity }}" required>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <label>Kargo</label>
        <select data-plugin="customselect" class="form-control" name="deliveryType" 
            data-placeholder="Arayın" data-select2-id="cargo" required>
            @foreach ($cargos as $id => $cargo)
            @php $selected = $incoming->deliveryType == $id ? 'selected' : null @endphp
            <option value="{{ $id }}" {{ $selected }}>{{ $cargo }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Kargo Tip</label>
        <select data-plugin="customselect" class="form-control" name="deliveryMessageType" 
            data-placeholder="Arayın" data-select2-id="cargo-type" required>
            @foreach ($cargo_types as $id => $type)
            @php $selected = $incoming->deliveryMessageType == $id ? 'selected' : null @endphp
            <option value="{{ $id }}" {{ $selected }}>{{ $type }}</option>
            @endforeach
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

    $('[name="categoryId"]').change(function()
    {
        var datas = 
        {
            type: 'ciceksepeti', 
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