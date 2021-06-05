@extends('product.list')

@section('title', 'Ürün - N11')

@section('name', 'N11')

@section('url', 'n11')

@section('content-list')

<a href="/urun/n11" class="btn btn-primary mb-3">
    <i class="uil uil-plus-circle"></i> Ürün Ekle
</a>

@if (isset($datas->pagingData->pageCount) && $datas->pagingData->pageCount > 1)
<form class="row mb-3 align-items-center border-bottom pb-3" id="page">
    <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
        <a go="{{ $page - 1 }}"><i class="uil uil-angle-left"></i></a>
        <select name="page" class="custom-select mx-1 w-auto">
            @for ($i = 1; $i <= $datas->pagingData->pageCount; $i++)
            
            <?php $selected = $page == $i ? 'selected' : '' ?>
            
            <option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
            @endfor
        </select>
        <a go="{{ $page + 1 }}"><i class="uil uil-angle-right"></i></a>
        <span class="ml-2">{{ $limit }} - {{ $limit + $show }}</span>
    </div>
    <div class="col-md-6 text-center text-md-right">
        <b>Toplam:</b> {{ $datas->pagingData->totalCount }}
    </div>
</form>
@endif
<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Ürün Kodu</th>
            <th>İsim</th>
            <th>Mağaza Ürün Kodu</th>
            <th>Fiyat</th>
            <th>İndirimli Fiyat</th>
            <th>Stok</th>
            <th>Para Birimi</th>
            <th>Eşleştir</th>
            <th>Satış</th>
            <th>Onay</th>
            <th>Yerli</th>
            <th>Ön Açıklama</th>
            <th>Satış</th>
            <th>Satış</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->products->product))
        @foreach ($datas->products->product as $data)

        <?php 

        if (!is_array($data->stockItems->stockItem))

            $data->stockItems->stockItem = [$data->stockItems->stockItem]

        ?>

        <tr data-code="{{ $data->productSellerCode }}" currency="{{ $data->currencyType }}">
            <td>
                <a href="/urun/n11/{{ $data->id }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>
            <td>{{ $data->id }}</td>
            <td>{{ $data->title }}</td>
            <td>{{ $data->productSellerCode }}</td>
            <td data-order="{{ $data->price }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->price }}" name="price">
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
            <td data-order="{{ $data->displayPrice }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->displayPrice }}" name="displayPrice">
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
            <td data-order="{{ $data->stockItems->stockItem[0]->quantity }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0" step="1"
                        value="{{ $data->stockItems->stockItem[0]->quantity }}" name="quantity">
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
            <td>{{ $data->currencyType }}</td>
            
            <?php
                
            $temp = 
            [
                \App\Models\Match::where('type', 'n11')
                    ->where('code', $data->productSellerCode)
                    ->first()
            ];

            if (!$temp[0])
            
                $temp = [new \App\Models\Match, new \App\Models\Product];

            else $temp[1] = \App\Models\Product::find($temp[0]->product_id);
            
            ?>            
            
            <td data-order="{{ $temp[1]->code }}">
                <div class="input-group" style="width: 175px">
                    <select name="match" data-plugin="customselect" 
                        class="form-control" data-value="{{ $temp[0]->product_id }}">
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
            <td>{{ $data->saleStatus }}</td>
            <td>{{ $data->approvalStatus }}</td>
            <td>
                @if ($data->isDomestic)
                <i class="uil uil-check"></i>
                @endif
            </td>
            <td>{{ $data->subtitle }}</td>
            <td>
                <button class="btn btn-success" task="StartSellingProductBySellerCode"
                    data-id="{{ $data->productSellerCode }}">
                    Satışı Başlat <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
            <td>
                <button class="btn btn-warning" task="StopSellingProductBySellerCode"
                    data-id="{{ $data->productSellerCode }}">
                    Satışı Durdur <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
        </tr>        
        <!-- variant -->
        @if (count($data->stockItems->stockItem) > 1)
        @foreach ($data->stockItems->stockItem as $item)

        @php 

            if ($item->sellerStockCode == $data->productSellerCode)

                continue;

        @endphp

        <tr data-code="{{ $item->sellerStockCode }}" currency="{{ $data->currencyType }}">
            <td>
                <a href="/urun/n11/{{ $data->id }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>
            <td>{{ $data->id }}</td>
            <td>{{ $data->title }}</td>
            <td>{{ $item->sellerStockCode }}</td>
            <td data-order="{{ $item->optionPrice }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $item->optionPrice }}" name="price" disabled>
                    <div class="input-group-append">
                        <button class="input-group-text" disabled>
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td data-order="{{ $item->displayPrice }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $item->displayPrice }}" name="displayPrice" disabled>
                    <div class="input-group-append">
                        <button class="input-group-text" disabled>
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td data-order="{{ $item->quantity }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0" step="1"
                        value="{{ $item->quantity }}" name="quantity">
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
            <td>{{ $data->currencyType }}</td>
            
            <?php
                
            $temp = 
            [
                \App\Models\Match::where('type', 'n11')
                    ->where('code', $item->sellerStockCode)
                    ->first()
            ];

            if (!$temp[0])
            
                $temp = [new \App\Models\Match, new \App\Models\Product];

            else $temp[1] = \App\Models\Product::find($temp[0]->product_id);
            
            ?>            
            
            <td data-order="{{ $temp[1]->code }}">
                <div class="input-group" style="width: 175px">
                    <select name="match" data-plugin="customselect" 
                        class="form-control" data-value="{{ $temp[0]->product_id }}">
                        <option value="">- Yok</option>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->code }}</option>
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
            <td>{{ $data->saleStatus }}</td>
            <td>{{ $data->approvalStatus }}</td>
            <td>
                @if ($data->isDomestic)
                <i class="uil uil-check"></i>
                @endif
            </td>
            <td>{{ $data->subtitle }}</td>
            <td>
                <button class="btn btn-success" task="StartSellingProductBySellerCode"
                    data-id="{{ $item->sellerStockCode }}" disabled>
                    Satışı Başlat <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
            <td>
                <button class="btn btn-warning" task="StopSellingProductBySellerCode"
                    data-id="{{ $item->sellerStockCode }}" disabled>
                    Satışı Durdur <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
        </tr>
        @endforeach
        @endif
        <!-- variant -->
        @endforeach
        @else
        <tr>
            <td colspan="100%" class="text-center">
                {{ $datas->result->errorMessage }}
            </td>
        </tr>
        @endif
    </tbody>
</table>

@endsection

@section('script-list')

<script>

    $('#custom-datatable').DataTable(
    {
        order: [[2, 'asc']],
        columnDefs: 
        [
            {
                targets: [0, 13, 14],
                searchable: false,
                sortable: false
            },
            {
                targets: 7,
                render: function(data)
                {
                    var status = ['TL', 'USD', 'EUR'];

                        key = Number(data) - 1;

                    return status[key] != undefined ? status[key] : data;
                }
            },
            {
                targets: 9,
                render: function(data)
                {
                    var status = ['Satış Öncesi', 'Satışta', 'Stok Yok', 'Satışa Kapalı'];

                        key = Number(data) - 1;

                    return status[key] != undefined ? status[key] : data;
                }
            },
            {
                targets: 10,
                render: function(data)
                {
                    var status = ['Aktif', 'Beklemede', 'Yasaklı'];

                        key = Number(data) - 1;

                    return status[key] != undefined ? status[key] : data;
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

    @if (isset($datas->pagingData->pageCount) && $datas->pagingData->pageCount > 1)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == $datas->pagingData->pageCount)
            $('#page a:eq(1)').attr('go', '{{ $datas->pagingData->pageCount }}');
        @elseif ($page > $datas->pagingData->pageCount)
            location.href = '/urunler/n11?page={{ $datas->pagingData->pageCount - 1 }}';
        @endif

        function page(go)
        {
            $('[name="page"]').val(go);

            $('.content form').trigger('submit');
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

        var _this = $(this), _input;

        if ($(this).closest('td').attr('class') == undefined)

            $(this).closest('td').attr('class', '');

        if ($(this).closest('td').attr('class').indexOf('child') != -1)

            _input = $(this).closest('li').find('input, select').eq(0);

        else _input = $(this).closest('td').find('input, select').eq(0);

        var datas = 
            {
                type: 'n11',
                task: _input.attr('name'),
                value: _input.val(),
                id: _input.closest('tr').attr('data-code'),
                currency: _input.closest('tr').attr('currency')
            };

        _this.find('i').hide();

        _this.find('div').show();
        
        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp == true)
            {
                _input.attr('data-value', datas.value);

                _this.find('i').attr('class', 'uil uil-check-circle');
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
                type: 'n11',
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