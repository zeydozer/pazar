@extends('product.list')

@section('title', 'Ürün - Çiçeksepeti')

@section('name', 'Çiçeksepeti')

@section('url', 'ciceksepeti')

@section('content-list')

<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <a href="/urun/ciceksepeti" class="btn btn-primary">
            <i class="uil uil-plus-circle"></i> Ürün Ekle
        </a>
    </div>
    <form class="col-md-4">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tedarikçi Varyant Kodu" name="code" 
                required value="{{ Request::get('code') }}">
            <div class="input-group-append">
                <button class="input-group-text">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@if (isset($datas->totalCount) && $datas->totalCount > $show)
<form class="row mb-3 align-items-center border-bottom pb-3" id="page">
    <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
        <a go="{{ $page - 1 }}"><i class="uil uil-angle-left"></i></a>
        <select name="page" class="custom-select mx-1 w-auto">
            @for ($i = 1; $i <= ceil($datas->totalCount / $show); $i++)
            
            <?php $selected = $page == $i ? 'selected' : '' ?>
            
            <option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
            @endfor
        </select>
        <a go="{{ $page + 1 }}"><i class="uil uil-angle-right"></i></a>
        <span class="ml-2">{{ $limit }} - {{ $limit + $show }}</span>
    </div>
    <div class="col-md-6 text-center text-md-right">
        <b>Toplam:</b> {{ $datas->totalCount }}
    </div>
</form>
@endif
<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Fotoğraf</th>
            <th>İsim</th>
            <th>Kategori</th>
            <th>Aktif</th>
            <th>Durum</th>
            <th>Fiyat</th>
            <th>İndirimli Fiyat</th>
            <th>Stok</th>
            <th>Eşleştir</th>
            <th>Kod</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->products))
        @foreach ($datas->products as $data)        
        <tr stock-code="{{ $data->stockCode }}">
            <td>
                <a href="/urun/ciceksepeti/{{ $data->stockCode }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>
            <td>
                @if (count($data->images) > 0)
                {{ $data->images[0] }}
                @endif
            </td>
            <td>{{ $data->productName }}</td>
            <td>{{ $data->categoryName }}</td>
            <td>
                @if ($data->isActive)
                <i class="uil uil-check mr-1"></i>
                @endif
            </td>
            <td class="text-capitalize">{{ $data->productStatusType }}</td>
            <td data-order="{{ $data->listPrice }}">
                <div class="input-group" stock-code="{{ $data->stockCode }}">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->listPrice }}" name="listPrice">
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
            <td data-order="{{ $data->salesPrice }}">
                <div class="input-group" stock-code="{{ $data->stockCode }}">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->salesPrice }}" name="salesPrice">
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
            <td data-order="{{ $data->stockQuantity }}">
                <div class="input-group" style="width: 100px" stock-code="{{ $data->stockCode }}">
                    <input type="number" class="form-control" placeholder="0" step="1"
                        value="{{ $data->stockQuantity }}" name="stockQuantity">
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
                \App\Models\Match::where('type', 'ciceksepeti')
                    ->where('code', $data->stockCode)
                    ->first()
            ];

            if (!$temp[0])
            
                $temp = [new \App\Models\Match, new \App\Models\Product];

            else $temp[1] = \App\Models\Product::find($temp[0]->product_id);
            
            ?>            
            
            <td data-order="{{ $temp[1]->code }}" data-search="{{ $temp[1]->code }}">
                <div class="input-group" style="width: 175px" stock-code="{{ $data->stockCode }}">
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
            <td>{{ $data->productCode }}</td>
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
                targets: 1,
                searchable: false,
                sortable: false,
                render: function(data) 
                {
                    if (data != '')
                    
                        return '<a href="'+ data +'" data-fancybox><img src="'+ data +'" width="50"></a>';

                    else return '<i class="uil uil-minus"></i>';
                }
            },
            {
                targets: 5,
                render: function(data) 
                {
                    return data.toLowerCase();
                }
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
            var select = $('[data-dtr-index="9"][data-dt-row="'+ row.index() +'"] select');

            select.select2().val(select.attr('data-value')).trigger('change');
        }
    });

    @if (isset($datas->totalCount) && $datas->totalCount > $show)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == ceil($datas->totalCount / $show))
            $('#page a:eq(1)').attr('go', '{{ ceil($datas->totalCount / $show) }}');
        @elseif ($page > ceil($datas->totalCount / $show))
            location.href = '/urunler/ciceksepeti?page={{ ceil($datas->totalCount / $show) - 1 }}';
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
                type: 'ciceksepeti',
                task: _input.attr('name'),
                code: _input.closest('.input-group').attr('stock-code')
            };

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

</script>

@endsection