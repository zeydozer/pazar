@extends('product.edit')

@section('title', 'Ürün - Sistem - Kayıt')

@section('name', 'Sistem')

@section('url', null)

@section('content-edit')

<div class="form-group">
    <select data-plugin="customselect" class="form-control" name="id" 
        onchange='location.href = "/urun/"+ value'>
        <option value="" selected>+ Yeni</option>
        @foreach ($datas as $data)
        
        <?php $select = $data->id == $incoming->id ? 'selected' : ''; ?>
        
        <option value="{{ $data->id }}" {{ $select }}>{{ $data->name }}</option>
        @endforeach
    </select>
</div>
<hr>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a href="#core-info" data-toggle="tab" aria-expanded="false"
            class="nav-link active">
            <span class="d-block d-sm-none"><i class="uil-info-circle"></i></span>
            <span class="d-none d-sm-block">Temel</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#content-info" data-toggle="tab" aria-expanded="false"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-files-landscapes"></i></span>
            <span class="d-none d-sm-block">İçerik</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#sales-policy" data-toggle="tab" aria-expanded="true"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-cart"></i></span>
            <span class="d-none d-sm-block">Satış</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a href="#other-info" data-toggle="tab" aria-expanded="false"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-folder-plus"></i></span>
            <span class="d-none d-sm-block">Diğer</span>
        </a>
    </li>
</ul>
<div class="tab-content p-3 text-muted">
    <div class="tab-pane show active" id="core-info">
        <div class="form-group dropzone" action="/dropzone">
            <div class="fallback">
                <input name="file" type="file" multiple />
            </div>
            <div class="dz-message needsclick">
                <i class="h1 text-muted  uil-cloud-upload"></i>
                <h3>Resimleri buraya bırakın veya yüklemek için tıklayın.</h3>
                <!-- @if (count($photos) == 0)
                <span class="text-muted font-13">(Son resim <strong>profil</strong> olacak.)</span>
                @endif -->
            </div>
        </div>
        <div class="form-group">
            <label>Youtube Url</label>
            <input type="url" class="form-control" name="video" value="{{ $incoming->video }}"
            placeholder="https://www.youtube.com/watch?v=zlhK9Q0XaNY">
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Marka</label>
                <select data-plugin="customselect" class="form-control" name="brand_id">
                    <option value="" selected>- Yok</option>
                    @foreach ($brands as $id => $brand)
                    
                    <?php $select = $id == $incoming->brand_id ? 'selected' : ''; ?>
                    
                    <option value="{{ $id }}" {{ $select }}>{{ $brand }}</option>
                    @endforeach            
                </select>
            </div>
            <div class="col-md-4">
                <label>Ana Kategori</label>
                <select data-plugin="customselect" class="form-control" name="category_id">
                    <option value="" selected>- Yok</option>
                    @foreach ($categories as $id => $name)
                    
                    <?php $select = $id == $incoming->category_id ? 'selected' : ''; ?>
                    
                    <option value="{{ $id }}" {{ $select }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Diğer Kategori(ler)</label>
                <select data-plugin="customselect" class="form-control" name="category_id_other[]" multiple>
                    @php $incoming->category_id_other = explode(', ', $incoming->category_id_other) @endphp
                    @foreach ($categories as $id => $name)
                    
                    <?php $select = in_array($id, $incoming->category_id_other) ? 'selected' : ''; ?>
                    
                    <option value="{{ $id }}" {{ $select }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="attribute">
            @include('product.attribute-l', 
            [
                'type' => 'system', 
                'attributes' => $attributes,
            ])
        </div>
        <div class="form-group row align-items-center">
            <div class="col-md-5">
                <label>Ürün Adı</label>
                <input type="text" class="form-control" name="name" value="{{ $incoming->name }}" required>
            </div>
            <div class="col-md-5">
                <label>Ürün Resmi Adı</label>
                <input type="text" class="form-control" name="name_invoice" value="{{ $incoming->name_invoice }}">
            </div>
            <div class="col-md-2">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="domestic" 
                    name="domestic" value="1" @if ($incoming->domestic) checked @endif>
                    <label class="custom-control-label" for="domestic">Yerli</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Stok Kodu</label>
                <input type="text" class="form-control" name="code" value="{{ $incoming->code }}" required>
            </div>
            <div class="col-md-4">
                <label>Model Kodu</label>
                <input type="text" class="form-control" name="model_code" value="{{ $incoming->model_code }}">
            </div>
            <div class="col-md-4">
                <label>Barkod</label>
                <input type="text" class="form-control" name="barcode" value="{{ $incoming->barcode }}">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label>Anahtar Kelime</label>
                <input type="text" class="form-control" name="keyword" value="{{ $incoming->keyword }}">
            </div>
            <div class="col-md-6">
                <label>Url</label>
                <input type="text" class="form-control" name="url" value="{{ $incoming->url }}">
            </div>
        </div>
        <div class="form-group">
            <label>Ön Açıklama</label>
            <input type="text" class="form-control" name="pre_description" value="{{ $incoming->pre_description }}">
        </div>
        <div class="form-group row">
            <div class="col-md-3">
                <label>Stok</label>
                <input data-toggle="touchspin" type="text" name="stock" data-max="99999999"
                    value="{{ $incoming->stock }}" required disabled>
            </div>
            <div class="col-md-3">
                <label>En <small>(cm)</small></label>
                <input data-toggle="touchspin" type="text" name="width" data-max="99999999"
                    value="{{ $incoming->width }}" data-step="0.1" data-decimals="1" required>
            </div>
            <div class="col-md-3">
                <label>Boy <small>(cm)</small></label>
                <input data-toggle="touchspin" type="text" name="length" data-max="99999999"
                    value="{{ $incoming->length }}" data-step="0.1" data-decimals="1" required>
            </div>
            <div class="col-md-3">
                <label>Yükseklik <small>(cm)</small></label>
                <input data-toggle="touchspin" type="text" name="height" data-max="99999999"
                    value="{{ $incoming->height }}" data-step="0.1" data-decimals="1" required>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="content-info">
        <div class="form-group">
            <label>Açıklama</label>
            <textarea id="summernote-editor" name="description">
                
                <?php echo $incoming->description ?>
            
            </textarea>
        </div>
        <div class="form-group" id="bullet">
            <label>Ürün Özellikleri</label>
            @foreach ($bullets as $bullet)
            <div class="input-group">
                <div class="input-group-prepend">
                    <a href="#" class="input-group-text">
                        <i class="uil uil-minus-circle"></i>
                    </a>
                </div>
                <input type="text" class="form-control" value="{{ $bullet }}" name="bullet[]">
                <div class="input-group-append">
                    <a href="#" class="input-group-text">
                        <i class="uil uil-plus-circle"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="tab-pane" id="sales-policy">
        <div class="form-group row">    
            <div class="col-md-3">
                <label>Min Fiyat</label>
                <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                    data-decimals="2" value="{{ $incoming->price_min }}" name="price_min" 
                    data-max="99999999">
            </div>
            <div class="col-md-3">
                <label>Fiyat</label>
                <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                    data-decimals="2" value="{{ $incoming->price }}" name="price" 
                    data-max="99999999" required>
            </div>
            <div class="col-md-3">
                <label>İndirimli Fiyat</label>
                <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                    data-decimals="2" value="{{ $incoming->discount }}" name="discount" 
                    data-max="99999999">
            </div>
            <div class="col-md-3">
                <label>Kdv</label>
                <select name="tax" class="form-control" required>
                    @foreach ([1, 8, 18] as $tax)
                    @php $select = $incoming->tax == $tax ? 'selected' : null @endphp
                    <option value="{{ $tax }}" {{ $select }}>%{{ $tax }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12 mb-1">
                <strong>Paketlenmiş</strong>
            </div>
            <div class="col-md-9">
                <div class="row" id="deci">
                    <div class="col-md-4">
                        <label>En <small>(cm)</small></label>
                        <input data-toggle="touchspin" type="text" name="width_p" data-max="99999999"
                            value="{{ $incoming->width_p }}" data-step="0.1" data-decimals="1" required>
                    </div>
                    <div class="col-md-4">
                        <label>Boy <small>(cm)</small></label>
                        <input data-toggle="touchspin" type="text" name="length_p" data-max="99999999"
                            value="{{ $incoming->length_p }}" data-step="0.1" data-decimals="1" required>
                    </div>
                    <div class="col-md-4">
                        <label>Yükseklik <small>(cm)</small></label>
                        <input data-toggle="touchspin" type="text" name="height_p" data-max="99999999"
                            value="{{ $incoming->height_p }}" data-step="0.1" data-decimals="1" required>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label>Desi</label>
                <input type="text" name="deci" value="{{ $incoming->deci }}" disabled class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Hazırlık Süresi</label>
            <input data-toggle="touchspin" type="text" name="prepare" data-max="99999999"
                value="{{ $incoming->prepare }}" required data-bts-postfix="Gün">
        </div>
    </div>
    <div class="tab-pane" id="other-info">
        <div class="form-group row align-items-center">
            <div class="col-12 mb-1">
                <strong>Diğer Bilgiler</strong>
            </div>
            <div class="col-md-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="assembly" 
                    name="assembly" value="1" @if ($incoming->assembly) checked @endif>
                    <label class="custom-control-label" for="assembly">Montaj</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="battery" 
                    name="battery" value="1" @if ($incoming->battery) checked @endif>
                    <label class="custom-control-label" for="battery">Pilli</label>
                </div>
            </div>
            <div class="col-md-4">
                <label>Üretici</label>
                <input type="text" class="form-control" name="manifacturer" value="{{ $incoming->manifacturer }}">
            </div>
            <div class="col-md-6">
                <label>Kutu İçeriği</label>
                <input type="text" class="form-control" name="description_box" value="{{ $incoming->description_box }}">
            </div>
            <div class="col-md-6">
                <label>Güvenlik Metni</label>
                <input type="text" class="form-control" name="security" value="{{ $incoming->security }}">
            </div>
        </div>
        <div class="form-group row align-items-center battery-info" style="@if (!$incoming->battery) display: none @endif">
            @php $battery_info = json_decode($incoming->battery_info, true) @endphp
            <div class="col-md-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="battery_in" name="battery_info[0]" 
                    value="1" @if (isset($battery_info[0]) && $battery_info[0]) checked @endif>
                    <label class="custom-control-label" for="battery_in">Pil içinde mi?</label>
                </div>
            </div>
            <div class="col-md-3">
                <label>Pil Adet</label>
                <input data-toggle="touchspin" type="text" name="battery_info[1]" data-max="99999999" 
                @if (isset($battery_info[1])) value="{{ $battery_info[1] }}" @endif>
            </div>
            <div class="col-md-3">
                <label>Pil Boyutu</label>
                <input type="text" class="form-control" name="battery_info[2]" 
                @if (isset($battery_info[2])) value="{{ $battery_info[2] }}" @endif>
            </div>
            <div class="col-md-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="battery_change" name="battery_info[3]" 
                    value="1" @if (isset($battery_info[3]) && $battery_info[3]) checked @endif>
                    <label class="custom-control-label" for="battery_change">Kullanıcı pili değiştirebiliyor mu?</label>
                </div>
            </div>
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
    <hr>
    @foreach (\App\Models\Data::orderBy('name')->where('active', 1)->get() as $i => $temp)
    <a class="btn btn-success d-inline-flex align-items-center 
        justify-content-center @if ($i != 0) ml-2 @endif" 
        href="/urun/{{ $temp->name }}?s={{ $incoming->id }}">
        <div class="spinner-grow text-white mr-2" role="status" style="display: none">
            <span class="sr-only">Yükleniyor...</span>
        </div>
        <span><i class="uil uil- uil- uil-compress-alt-left mr-1"></i>{{ ucfirst($temp->name) }}</span>
    </a>
    @endforeach
    @endif

</div>

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

    #bullet .input-group:not(:last-child)
    {
        margin-bottom: 1rem;
    }

    .dz-preview
    {
        z-index: 99 !important;
    }

</style>

@endsection

@section('script-edit')

<script>

    $('.dropzone').sortable(
    {
        stop: function(event, ui) 
        {
            datas = {};
            
            $('[name="photo[]"]').each(function(i)
            { 
                datas[i + 1] = $(this).val();
            });

            $.get('/urun/foto/{{ $incoming->id }}', datas);
        },

        containment: '.dropzone',
    });

    $('body').on('change', '#deci [data-toggle="touchspin"]', function()
    {
        var deci = 1;

        $('#deci [data-toggle="touchspin"]').each(function()
        {
            deci = deci * $(this).val();
        });

        deci = Number(deci / 3000).toFixed(2);

        $('[name="deci"]').val(deci);
    });

    $('body').on('click', '#bullet .input-group-prepend a, #bullet .input-group-append a', function(e)
    {
        e.preventDefault();

        var group = $(this).closest('.input-group').html();
            
            group = '<div class="input-group">'+ group +'</div>';
            
            button = $(this).parent().attr('class');
            
            order = $('.'+ button +' a').index(this);
            
            container = '#'+ $(this).closest('.form-group').attr('id');

        if (button.indexOf('append') != -1)
        {
            $(group).insertAfter(container +' .input-group:eq('+ order +')');

            $(container +' .input-group').eq(order + 1).find('input, select').val(null);
        }

        else
        {
            if ($(container +' .input-group').length > 1)
                
                $(this).closest('.input-group').remove();

            else $(this).closest('.input-group').find('input').val(null);
        }
    });

    $('body').on('change', '#battery', function()
    {
        if ($(this).is(':checked'))
        {
            $('.battery-info').slideDown(
            {
                start: function() 
                {
                    $(this).css({display: 'flex'})
                }
            
            }, 250);
        }

        else $('.battery-info').slideUp(250);
    });

    @if ($incoming->id)
        @foreach (\DB::table('product_a')->where('product_id', $incoming->id)->get() as $attribute)
            
            var attribute = $('[name="attribute[{{ $attribute->attribute_id }}]"]');
            
            attribute.val('{{ $attribute->value }}');

            if (attribute.attr('data-select2-id') != undefined)

                attribute.trigger('change');
        
        @endforeach
    @endif

    $('[name="category_id"]').change(function()
    {
        var datas = 
        {
            type: 'system', 
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

                @if ($incoming->id)
                    @foreach (\DB::table('product_a')->where('product_id', $incoming->id)->get() as $attribute)
                    
                        var attribute = $('[name="attribute[{{ $attribute->attribute_id }}]"]');
                        
                        attribute.val('{{ $attribute->value }}');

                        console.log(attribute.attr('data-select2-id'))

                        if (attribute.attr('data-select2-id') != undefined)

                            attribute.trigger('change');
                    
                    @endforeach
                @endif
            }

            else $('#attribute').html(null);
        });
    });

    Dropzone.prototype.defaultOptions.complete = function(a) 
    {
        if (a._removeLink && (a._removeLink.innerHTML = this.options.dictRemoveFile), a.previewElement)
        {   
            if (a.status == "success")
            {
                var checked = a.profile == 1 ? 'checked' : '';

                if (a.order == undefined)

                    a.order = $('[name="photo[]"]').length;

                a.input_profile = Dropzone.createElement('<div class="custom-control custom-radio mt-1 text-center"><input '+ checked +' type="radio" id="profile-select-'+ a.order +'" name="profile-select" class="custom-control-input" value='+ a.xhr.response +'><label class="custom-control-label" for="profile-select-'+ a.order +'">Profil</label></div>');

                a.previewElement.appendChild(a.input_profile);

                a.input_photo = Dropzone.createElement("<input type='hidden' name='photo[]' value="+ a.xhr.response +">");

                a.previewElement.appendChild(a.input_photo);

                for (var c = a.previewElement.querySelectorAll("[data-dz-fancybox]"), d = 0, c = c; ; )
                {
                    var e;

                    if (d >= c.length)
                        
                        break;
                    
                    e = c[d++];
                    
                    var f = e;
                    
                    f.href = '/assets/images/uploads/'+ a.xhr.response.replace(/"/g, "");
                }
            }

            return a.previewElement.classList.add("dz-complete")
        }
    };

    @if (count($photos) > 0)

    Dropzone.prototype.defaultOptions.init = function(a)
    {
        @foreach ($photos as $i => $photo)

        var mockFile = 
        {
            name: '{{ $photo->name }}',
            xhr: {response: '{{ $photo->name }}'}, 
            size: Number('{{ filesize("assets/images/products/". $photo->name) }}'),
            status: "success",
            order: Number('{{ $i }}'),
            profile: Number('{{ $photo->profile == 1 ? 1 : 0 }}'),
        };
  
        this.emit("addedfile", mockFile);
        this.emit("thumbnail", mockFile, '/assets/images/products/{{ $photo->name }}');
        this.emit("success", mockFile);
        this.emit("complete", mockFile);

        @endforeach
    }

    @endif

</script>

@endsection