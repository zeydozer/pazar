@extends('product.list')

@section('title', 'Ürün - Hepsiburada')

@section('name', 'Hepsiburada')

@section('url', 'hepsiburada')

@section('content-list')

<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <a href="/urun/hepsiburada" class="btn btn-primary">
            <i class="uil uil-plus-circle"></i> Ürün Ekle
        </a>
    </div>
</div>
@if (isset($datas->TotalCount) && $datas->TotalCount > $show)
<form class="row mb-3 align-items-center border-bottom pb-3" id="page">
    <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
        <a go="{{ $page - 1 }}"><i class="uil uil-angle-left"></i></a>
        <select name="page" class="custom-select mx-1 w-auto">
            @for ($i = 1; $i <= ceil($datas->TotalCount / $show); $i++)
            
            <?php $selected = $page == $i ? 'selected' : '' ?>
            
            <option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
            @endfor
        </select>
        <a go="{{ $page + 1 }}"><i class="uil uil-angle-right"></i></a>
        <span class="ml-2">{{ $limit }} - {{ $limit + $show }}</span>
    </div>
    <div class="col-md-6 text-center text-md-right">
        <b>Toplam:</b> {{ $datas->TotalCount }}
    </div>
</form>
@endif
<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Katalog Kod</th>
            <th>Stok Kod</th>
            <th>Satış</th>
            <th>Fiyat</th>
            <th>Stok</th>
            <th>Eşleştir</th>
            <th>Askıda</th>
            <th>Kilitli</th>
            <th>Dondurulma</th>
            <th>Satış</th>
            <th>Satış</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->Listings->Listing))
        @foreach ($datas->Listings->Listing as $data)        
        <tr stock-code="{{ $data->MerchantSku }}">
            <td>
                <a href="/urun/hepsiburada/{{ $data->MerchantSku }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>
            <td>{{ $data->HepsiburadaSku }}</td>
            <td>{{ $data->MerchantSku }}</td>
            <td data-order="{{ $data->IsSalable }}">
                @if ($data->IsSalable == "true")
                <i class="uil uil-check mr-1"></i>
                @else
                <i class="uil uil-minus mr-1"></i>
                @endif
            </td>
            <td data-order="{{ $data->Price }}">
                <div class="input-group" stock-code="{{ $data->MerchantSku }}" stock-code-h="{{ $data->HepsiburadaSku }}"
                    price="{{ $data->Price }}" dispatch="{{ $data->DispatchTime }}" quantity="{{ $data->AvailableStock }}">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->Price }}" name="Price">
                    <div class="input-group-append">
                        <button class="input-group-text">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td data-order="{{ $data->AvailableStock }}">
                <div class="input-group" style="width: 100px" stock-code="{{ $data->MerchantSku }}" stock-code-h="{{ $data->HepsiburadaSku }}"
                    price="{{ $data->Price }}" dispatch="{{ $data->DispatchTime }}" quantity="{{ $data->AvailableStock }}">
                    <input type="number" class="form-control" placeholder="0" step="1"
                        value="{{ $data->AvailableStock }}" name="AvailableStock">
                    <div class="input-group-append">
                        <button class="input-group-text">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                </div>
            </td>
            
            <?php
                
            $temp = 
            [
                \App\Models\Match::where('type', 'hepsiburada')
                    ->where('code', $data->MerchantSku)
                    ->first()
            ];

            if (!$temp[0])
            
                $temp = [new \App\Models\Match, new \App\Models\Product];

            else $temp[1] = \App\Models\Product::find($temp[0]->product_id);
            
            ?>            
            
            <td data-order="{{ $temp[1]->code }}" data-search="{{ $temp[1]->code }}">
                <div class="input-group" style="width: 175px" stock-code="{{ $data->MerchantSku }}">
                    <select name="match" data-plugin="customselect" class="form-control" 
                        data-value="{{ $temp[0]->product_id }}">
                        <option value="">- Yok</option>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ implode(' ', [$product->brand->name, $product->code]) }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="input-group-text">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td data-order="{{ $data->IsSuspended }}">
                @if ($data->IsSuspended == "true")
                <i class="uil uil-check mr-1"></i>
                @else
                <i class="uil uil-minus mr-1"></i>
                @endif
            </td>
            <td data-order="{{ $data->IsLocked }}">
                @if ($data->IsLocked == "true")
                <i class="uil uil-check mr-1"></i>
                @else
                <i class="uil uil-minus mr-1"></i>
                @endif
            </td>
            <td data-order="{{ $data->IsFrozen }}">
                @if ($data->IsFrozen == "true")
                <i class="uil uil-check mr-1"></i>
                @else
                <i class="uil uil-minus mr-1"></i>
                @endif
            </td>
            <td>
                <button class="btn btn-success" task="activate"
                    data-id="{{ $data->HepsiburadaSku }}">
                    Satışı Başlat <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
            <td>
                <button class="btn btn-warning" task="deactivate"
                    data-id="{{ $data->HepsiburadaSku }}">
                    Satışı Durdur <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
        </tr>
        @endforeach
        @elseif (isset($datas->message))
        <tr>
            <td colspan="100%" class="text-center">
                {{ $datas->message }}
            </td>
        </tr>
        @endif
    </tbody>
</table>

@endsection

@section('script-list')

<script>

    var table = $('#custom-datatable').DataTable(
    {
        order: [[2, 'asc']],
        columnDefs: 
        [
            {
                targets: 0,
                searchable: false,
                sortable: false
            },
            {
                targets: '_all',
                render: function(data)
                {
                    return data == '' || data == '0' ? 
                    
                        '<i class="uil uil-minus"></i>' :

                        data;
                }
            }
        ]
    });

    table.on('responsive-display', function (e, datatable, row, showHide, update) 
    {
        if (showHide)
        {
            var select = $('[data-dtr-index="5"][data-dt-row="'+ row.index() +'"] select');

            select.select2().val(select.attr('data-value')).trigger('change');
        }
    });

    @if (isset($datas->TotalCount) && $datas->TotalCount > $show)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == ceil($datas->TotalCount / $show))
            $('#page a:eq(1)').attr('go', '{{ ceil($datas->TotalCount / $show) }}');
        @elseif ($page > ceil($datas->TotalCount / $show))
            location.href = '/urunler/hepsiburada?page={{ ceil($datas->TotalCount / $show) - 1 }}';
        @endif

        function page(go)
        {
            $('[name="page"]').val(go);

            $('#page').trigger('submit');
        }

        $('[go]').click(function() 
        { 
            page($(this).attr('go')); 
        });

        $('[name="page"]').change(function() 
        { 
            page($(this).val()); 
        });
    @endif

    $('body').on('click', '#custom-datatable button.input-group-text', function(e)
    {
        e.preventDefault();

        var _this = $(this);

            _input = _this.closest('.input-group').find('input, select').eq(0);

            datas = 
            {
                type: 'hepsiburada',
                task: _input.attr('name'),
                code: _input.closest('.input-group').attr('stock-code'),
                code_h: _input.closest('.input-group').attr('stock-code-h'),
                dispatch: _input.closest('.input-group').attr('dispatch'),
            };

        if (datas.task == 'AvailableStock')

            datas.Price = _input.closest('.input-group').attr('price');

        else if (datas.task == 'Price')

            datas.AvailableStock = _input.closest('.input-group').attr('quantity');

        $('#custom-datatable .input-group[stock-code="'+ datas.code +'"]').each(function()
        {
            __input = $(this).find('input, select').eq(0);

            datas[__input.attr('name')] = __input.val();
        });

        _this.find('i').hide();

        _this.find('div').show();
        
        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp == true)
            {
                _input.attr('data-value', datas.value);

                _this.find('i').attr('class', 'uil uil-check-circle');

                if (datas.task == 'Price')
                
                    $('[stock-code="'+ datas.code +'"]').attr('price', datas.Price);

                else if (datas.task == 'AvailableStock')

                    $('[stock-code="'+ datas.code +'"]').attr('quantity', datas.AvailableStock);                

                /* setTimeout(function()
                {
                    _this.find('i').attr('class', 'uil uil-refresh');

                }, 2000); */
            }

            else _this.find('i').attr('class', 'uil uil-exclamation-circle');

            _this.find('div').hide();

            _this.find('i').show();
        
        }).fail(function(a, b, error) 
        {
            _this.find('div').hide();

            _this.find('i').attr('class', 'uil uil-exclamation-circle').show();
        });
    });

    $('body').on('click', 'button.btn', function(e)
    {
        e.preventDefault();

        var _this = $(this);

            datas = 
            {
                type: 'hepsiburada',
                task: _this.attr('task'),
                id: _this.attr('data-id'),
            };

        _this.find('i').hide();

        _this.find('div').css('display', 'inline-block');

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp == true)
            
                _this.find('i').attr('class', 'uil uil-check-circle');

            else _this.find('i').attr('class', 'uil uil-exclamation-circle');

            _this.find('div').hide();

            _this.find('i').show();

        }).fail(function(a, b, error) 
        {
            _this.find('div').hide();

            _this.find('i').attr('class', 'uil uil-exclamation-circle').show();
        });
    });

</script>

@endsection