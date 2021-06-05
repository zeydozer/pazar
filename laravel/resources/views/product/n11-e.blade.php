@extends('product.edit')

@section('title', 'Ürün - N11 - Kayıt')

@section('name', 'N11')

@section('url', 'n11')

@section('content-edit')

@if (isset($incoming->images->image))

<?php 

if (!is_array($incoming->images->image))

    $incoming->images->image = [$incoming->images->image];

?>

@if (count($incoming->images->image) > 0)
<div class="row mb-3" id="photos">
    @foreach ($incoming->images->image as $i => $photo)
    <div class="col-lg-3 col-md-4">
        <a href="{{ $photo->url }}" data-fancybox="pro">
            <img src="{{ $photo->url }}">
        </a>
        <input type="hidden" name="images[image][{{ $i }}][url]" value="{{ $photo->url }}">
        <input type="hidden" name="images[image][{{ $i }}][order]" value="{{ $i + 1 }}">
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
<div class="form-group" id="categories">
    <label>Kategori</label>
    <div class="input-group">
        <select data-plugin="customselect" class="form-control" name="category[id]">
            <option value="" selected>- Yok</option>            
            @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row align-items-center">
    <div class="col-md-6">
        <label>Ürün Adı</label>
        <input type="text" class="form-control" name="title" value="{{ $incoming->title }}" required>
    </div>
    <div class="col-md-4">
        <label>Stok Kodu</label>
        <input type="text" class="form-control" name="productSellerCode" 
            value="{{ $incoming->productSellerCode }}" required>
    </div>
    <div class="col-md-2">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="domestic" name="domestic" 
            @if (isset($incoming->domestic) && $incoming->domestic) checked @endif>
            <label class="custom-control-label" for="domestic">Yerli</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Ön Açıklama</label>
    <input type="text" class="form-control" name="subtitle" value="{{ $incoming->subtitle }}">
</div>
<div class="form-group">
    <label>Açıklama</label>
    <textarea id="summernote-editor" name="description">
        
        <?php echo $incoming->description ?>
    
    </textarea>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label>Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" name="price" required value="{{ $incoming->price }}"
            data-max="999999">
    </div>
    <div class="col-md-4">
        <label>İndirimli Fiyat</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
            data-decimals="2" name="discount[value]" required value="{{ $incoming->displayPrice }}"
            data-max="999999">
        <input type="hidden" name="discount[type]" value="3">
        <input type="hidden" name="discount[startDate]">
        <input type="hidden" name="discount[endDate]">
    </div>
    <div class="col-md-4">
        <label>Para Birimi</label>
        <select name="currencyType" class="form-control">
            @foreach (['TL', 'USD', 'EUR'] as $key => $name)
            
            <?php $select = $incoming->currencyType == $key + 1 ? 'selected' : null ?>

            <option value="{{ $key + 1 }}" {{ $select }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label>Durum</label>
        <select name="productCondition" class="form-control" required>
            <option value="1">Yeni</option>
            <option value="2">2. El</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>Kargo Süresi</label>
        <input data-toggle="touchspin" type="text" data-bts-postfix="Gün" data-step="1" 
            data-decimals="0" name="preparingDay" value="{{ $incoming->preparingDay }}"
            data-max="999999" required>
    </div>
    <div class="col-md-4">
        <label>Teslimat</label>
        <select name="shipmentTemplate" class="form-control" required>
            <option value="">- Yok</option>
            @if (isset($shipments->shipmentTemplates->shipmentTemplate))
            
            <?php 
            
            if (!is_array($shipments->shipmentTemplates->shipmentTemplate))

                $shipments->shipmentTemplates->shipmentTemplate = [$shipments->shipmentTemplates->shipmentTemplate];
            
            ?>           
            
            @foreach ($shipments->shipmentTemplates->shipmentTemplate as $shipment)
            
            <?php $select = $incoming->shipmentTemplate == $shipment->templateName ? 'selected' : null ?>
            
            <option value="{{ $shipment->templateName }}" {{ $select }}>{{ $shipment->templateName }}</option>
            @endforeach
            @endif
        </select>
    </div>
</div>
<hr>
<div id="stocks">
    @if (isset($incoming->stockItems->stockItem))
    @if (is_array($incoming->stockItems->stockItem))
    @foreach ($incoming->stockItems->stockItem as $i => $item)
    <div class="stock">
        <div class="form-group row align-items-end">
            <div class="col-md-3">
                <label>Stok Kodu</label>
                <input type="text" class="form-control" name="stockItems[stockItem][{{ $i }}][sellerStockCode]"
                    value="@if (isset($item->sellerStockCode)) {{ $item->sellerStockCode }} @endif"
                    @if ($i == 0) readonly @endif>
            </div>
            <div class="col-md-3">
                <label>Stok</label>
                <input data-toggle="touchspin" type="text" data-step="1" data-decimals="0" 
                    name="stockItems[stockItem][{{ $i }}][quantity]" data-max="999999" 
                    value="{{ $item->quantity }}" required>
            </div>
            <div class="col-md-4">
                <label>Fiyat</label>
                <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                    data-decimals="2" name="stockItems[stockItem][{{ $i }}][optionPrice]"
                    value="{{ $item->optionPrice }}" data-max="999999" 
                    @if ($i == 0) readonly @endif>
            </div>
            @if ($i == 0)
            <div class="col-md-2">
                <a href="add" class="btn btn-info btn-block">
                    <i class="uil uil-plus-circle"></i>
                </a>
            </div>
            @else
            <div class="col-md-2">
                <a href="del" class="btn btn-warning btn-block">
                    <i class="uil uil-minus-circle"></i>
                </a>
            </div>
            @endif
            <input type="hidden" name="stockItems[stockItem][{{ $i }}][n11CatalogId]"
                value="@if (isset($item->n11CatalogId)) {{ $item->n11CatalogId }} @endif">
        </div>
        @if ($i == 0)
        <div class="attribute mt-3"></div>
        @endif

        <?php 
        
        if (!is_array($item->attributes->attribute))

            $item->attributes->attribute = [$item->attributes->attribute];
        
        ?>

        <div class="attribute-stock mt-3">
            <div class="form-group row mb-0">
                @foreach ($item->attributes->attribute as $j => $attribute)
                <div class="col-md-4 mb-3">
                    <label>Özellik</label>
                    <input type="text" name="stockItems[stockItem][{{ $i }}][attributes][attribute][{{ $j }}][name]"
                        class="form-control mb-2" placeholder="İsim" value="{{ $attribute->name }}">
                    <input type="text" name="stockItems[stockItem][{{ $i }}][attributes][attribute][{{ $j }}][value]"
                        class="form-control" placeholder="Değer" value="{{ $attribute->value }}">
                </div>
                @endforeach
                @if (count($item->attributes->attribute) < 3)
                @for ($j = 4 - count($item->attributes->attribute); $j < 3; $j++)
                <div class="col-md-4 mb-3">
                    <label>Özellik</label>
                    <input type="text" name="stockItems[stockItem][{{ $i }}][attributes][attribute][{{ $j }}][name]"
                        class="form-control mb-2" placeholder="İsim">
                    <input type="text" name="stockItems[stockItem][{{ $i }}][attributes][attribute][{{ $j }}][value]"
                        class="form-control" placeholder="Değer">
                </div>
                @endfor
                @endif
            </div>
        </div>
    </div>
    @endforeach
    @else

    <?php $item = $incoming->stockItems->stockItem ?>

    <div class="stock">
        <div class="form-group row align-items-end">
            <div class="col-md-3">
                <label>Stok Kodu</label>
                <input type="text" class="form-control" name="stockItems[stockItem][0][sellerStockCode]"
                    value="@if (isset($item->sellerStockCode)) {{ $item->sellerStockCode }} @endif" readonly>
            </div>
            <div class="col-md-3">
                <label>Stok</label>
                <input data-toggle="touchspin" type="text" data-step="1" data-decimals="0" 
                    name="stockItems[stockItem][0][quantity]" data-max="999999" required
                    value="{{ $item->quantity }}">
            </div>
            <div class="col-md-4">
                <label>Fiyat</label>
                <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                    data-decimals="2" name="stockItems[stockItem][0][optionPrice]" readonly
                    value="{{ $item->optionPrice }}" data-max="999999">
            </div>
            <div class="col-md-2">
                <a href="add" class="btn btn-info btn-block">
                    <i class="uil uil-plus-circle"></i>
                </a>
            </div>
            <input type="hidden" name="stockItems[stockItem][0][n11CatalogId]" 
                value="@if (isset($item->n11CatalogId)) {{ $item->n11CatalogId }} @endif">
        </div>
        <div class="attribute mt-3"></div>
        <div class="attribute-stock">
            <div class="form-group row mb-0">
                @for ($j = 20; $j < 23; $j++)
                <div class="col-md-4 mb-3">
                    <label>Özellik</label>
                    <input type="text" name="stockItems[stockItem][0][attributes][attribute][{{ $j }}][name]"
                        class="form-control mb-2" placeholder="İsim">
                    <input type="text" name="stockItems[stockItem][0][attributes][attribute][{{ $j }}][value]"
                        class="form-control" placeholder="Değer">
                </div>
                @endfor
            </div>
        </div>
    </div>
    @endif
    @else
    <div class="stock">
        <div class="form-group row align-items-end">
            <div class="col-md-3">
                <label>Stok Kodu</label>
                <input type="text" class="form-control" name="stockItems[stockItem][0][sellerStockCode]"
                    value="{{ $incoming->productSellerCode }}" readonly>
            </div>
            <div class="col-md-3">
                <label>Stok</label>
                <input data-toggle="touchspin" type="text" data-step="1" data-decimals="0" 
                    name="stockItems[stockItem][0][quantity]" data-max="999999" required
                    value="{{ $incoming->stock }}">
            </div>
            <div class="col-md-4">
                <label>Fiyat</label>
                <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                    data-decimals="2" name="stockItems[stockItem][0][optionPrice]" readonly
                    value="{{ $incoming->price }}" data-max="999999">
            </div>
            <div class="col-md-2">
                <a href="add" class="btn btn-info btn-block">
                    <i class="uil uil-plus-circle"></i>
                </a>
            </div>
            <input type="hidden" name="stockItems[stockItem][0][n11CatalogId]">
        </div>
        <div class="attribute mt-3"></div>
        <div class="attribute-stock">
            <div class="form-group row mb-0">
                @for ($j = 20; $j < 23; $j++)
                <div class="col-md-4 mb-3">
                    <label>Özellik</label>
                    <input type="text" name="stockItems[stockItem][0][attributes][attribute][{{ $j }}][name]"
                        class="form-control mb-2" placeholder="İsim">
                    <input type="text" name="stockItems[stockItem][0][attributes][attribute][{{ $j }}][value]"
                        class="form-control" placeholder="Değer">
                </div>
                @endfor
            </div>
        </div>
    </div>
    @endif
</div>
<hr class="mt-0">
<div class="form-group row">
    <div class="col-md-3">
        <label>Satış Başlangıç</label>
        <input type="text" class="form-control date-picker" name="saleStartDate" 
            value="{{ $incoming->saleStartDate }}">
    </div>
    <div class="col-md-3">
        <label>Satış Bitiş</label>
        <input type="text" class="form-control date-picker" name="saleEndDate" 
            value="{{ $incoming->saleEndDate }}">
    </div>
    <div class="col-md-3">
        <label>Üretim</label>
        <input type="text" class="form-control date-picker" name="productionDate" 
            value="@if (isset($incoming->productionDate)) {{ $incoming->productionDate }} @endif">
    </div>
    <div class="col-md-3">
        <label>Son Kullanma</label>
        <input type="text" class="form-control date-picker" name="expirationDate" 
            value="@if (isset($incoming->expirationDate)) {{ $incoming->expirationDate }} @endif">
    </div>
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

    $('[name="category[id]"]').attr('name', null).addClass('select-n11');

    $('#content').append($('<input>').attr(
    {
        type: 'hidden',
        name: 'category[id]',
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

        $('.attribute').html(null);

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

            else
            {
                $('[name="category[id]"]').val(datas.id);

                attributes(datas.id);
            }
        });
    });

    @if (isset($incoming->category->id))
        
        var datas = 
        {
            type: 'n11',
            task: 'category-parent',
            id: '{{ $incoming->category->id }}',
        
        }, time = 0;

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp != false)
            {
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
                    $('.select-n11').eq(resp.length).val('{{ $incoming->category->id }}').trigger('change');

                    $('[name="category[id]"]').val('{{ $incoming->category->id }}');

                }, time);
            }
        });

    @endif

    function attributes(value)
    {
        var datas = 
        {
            type: 'n11', 
            task: 'attribute', 
            id: value,
            incoming: '{{ $incoming->id }}',
        };

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                $('.attribute').html(resp);

                $('.attribute select').select2();

                $('.attribute').find('[required]').each(function()
                {
                    var index = $(this).closest('.form-group').find('.form-control').index(this);

                    if (index % 2 == 0)

                        index--;

                    var label = $(this).closest('.form-group').find('label').eq(index);

                    label.html(label.html() +' *');
                });
            }

            else $('.attribute').html(null);
        });
    }

    @if ($incoming->productCondition)

    $('[name="productCondition"]').val('{{ $incoming->productCondition }}');

    @endif

    function copy(input)
    {
        var name = 
        {
            productSellerCode: 'stockItems[stockItem][0][sellerStockCode]',
            price: 'stockItems[stockItem][0][optionPrice]',
        };

        $('[name="'+ name[input.attr('name')] +'"]').val(input.val());
    }

    $('[name="productSellerCode"], [name="price"]').change(function()
    {
        copy($(this));
    
    }).keyup(function() { copy($(this)); });

    $('[href="add"]').click(function(e)
    {
        e.preventDefault();

        var datas = 
        {
            type: 'n11',
            task: 'stock',
            length: $('.stock').length
        };

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp !== false)
            {
                $('#stocks').append(resp);

                $('#stocks .stock:last-child [data-toggle="touchspin"]').TouchSpin();

                $('#stocks .stock:last-child').find('[required]').each(function()
                {
                    var index = $(this).closest('.form-group').find('.form-control').index(this);

                        label = $(this).closest('.form-group').find('label').eq(index);

                    label.html(label.html() +' *');
                });
            }
        });
    });

    $('body').on('click', '[href="del"]', function(e)
    {
        e.preventDefault();

        $(this).closest('.stock').remove();
    });

    @if (isset($incoming->stockItems->stockItem))        
        @if (is_array($incoming->stockItems->stockItem))
        
        var value = '{{ $incoming->stockItems->stockItem[0]->quantity }}';

        @else

        var value = '{{ $incoming->stockItems->stockItem->quantity }}';
        
        @endif

        $('[name="stockItems[stockItem][0][quantity]"]').val(value);

        if ($('.stock').length > 1)
        {
            <?php
            
            foreach ($incoming->stockItems->stockItem as $i => $item) :

                if ($i == 0) 
                    
                    continue;
                
            ?>

            setTimeout(function()
            {


            }, time + 500);

            <?php endforeach ?>
        }

    @endif

    $('.date-picker').flatpickr({dateFormat: 'd/m/Y'});

</script>

@endsection