@extends('product.list')

@section('title', 'Ürün - Gittigidiyor')

@section('name', 'Gittigidiyor')

@section('url', 'gittigidiyor')

@section('content-list')

<div class="row mb-3" id="menu-buttons">
    <div class="col-md-2">
        <a href="/urun/gittigidiyor" class="btn btn-primary btn-block">
            <i class="uil uil-plus-circle"></i> Ürün Ekle
        </a>
    </div>
    <div class="col-md-2 offset-md-2">
        <a href="A" class="btn btn-primary btn-block">
            <i class="uil uil-check mr-1"></i> Aktif
        </a>
    </div>
    <div class="col-md-2">
        <a href="L" class="btn btn-primary btn-block">
            <i class="uil uil-clock mr-1"></i> Hazır
        </a>
    </div>
    <div class="col-md-2">
        <a href="S" class="btn btn-primary btn-block">
            <i class="uil uil-file-check mr-1"></i> Satılan
        </a>
    </div>
    <div class="col-md-2">
        <a href="U" class="btn btn-primary btn-block">
            <i class="uil uil-file-times mr-1"></i> Satılmayan
        </a>
    </div>
</div>

@if (isset($datas->productCount) && $datas->productCount > $show)
<form class="row mb-3 align-items-center border-bottom pb-3" id="page">
    <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
        <a go="{{ $page - 1 }}"><i class="uil uil-angle-left"></i></a>
        <select name="page" class="custom-select mx-1 w-auto">
            @for ($i = 1; $i <= ceil($datas->productCount / $show); $i++)
            
            <?php $selected = $page == $i ? 'selected' : '' ?>
            
            <option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
            @endfor
        </select>
        <a go="{{ $page + 1 }}"><i class="uil uil-angle-right"></i></a>
        <span class="ml-2">{{ $limit }} - {{ $limit + $show }}</span>
    </div>
    <div class="col-md-6 text-center text-md-right">
        <b>Toplam:</b> {{ $datas->productCount }}
    </div>
    <input type="hidden" name="status" value="{{ $status }}">
</form>
@else
<form><input type="hidden" name="status" value="{{ $status }}"></form>
@endif
<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Kod</th>
            <th>İsim</th>
            <th>Liste Fiyatı</th>
            <th>Fiyat</th>
            <th>Stok</th>
            <th>Eşleştir</th>
            <th>Satılan</th>
            <th>Bitiş</th>
            <th>Durum</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->products->product))
        @foreach ($datas->products->product as $data)
        <tr data-id="{{ $data->productId }}" item-id="{{ $data->itemId }}">
            <td>
                <a href="/urun/gittigidiyor/{{ $data->productId }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>
            <td>{{ $data->itemId }}</td>
            <td>{{ $data->product->title }}</td>
            
            <?php if (!isset($data->product->marketPrice)) $data->product->marketPrice = 0 ?>

            <td data-order="{{ $data->product->marketPrice }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->product->marketPrice }}" name="marketPrice">
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
            <td data-order="{{ $data->product->buyNowPrice }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->product->buyNowPrice }}" name="price">
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
            <td data-order="{{ $data->product->productCount }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->product->productCount }}" name="stock">
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
                \App\Models\Match::where('type', 'gittigidiyor')
                    ->where('code', $data->productId)
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
            <td>{{ $data->summary->soldCount }}</td>

            <?php
            
            $temp = explode(' ', $data->summary->endDate);

            list($day, $month, $year) = explode('/', $temp[0]);

            $temp = $year .'-'. $month .'-'. $day .' '. $temp[1];
            
            ?>


            <td data-sort="{{ $temp }}">
                {{ str_replace('/', '.', $data->summary->endDate) }}
            </td>
            <td>{{ $data->summary->listingStatus }}</td>
            <td>

                <?php
                
                $temp =
                [
                    'A' => ['finishEarly', 'Sonlandır'],
                    'L' => ['calculatePriceForShoppingCart', 'Aktif Et'],
                    'S' => ['updateStockAndActivateProduct', 'Aktif Et'],
                    'U' => ['updateStockAndActivateProduct', 'Aktif Et'],
                ]
                
                ?>

                <button class="btn btn-success" task="{{ $temp[$status][0] }}">
                    {{ $temp[$status][1] }} <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
        </tr>
        @endforeach
        @elseif (isset($datas->error->message))
        <tr>
            <td colspan="100%" class="text-center">
                {{ $datas->error->message }}
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
        order: [[1, 'asc']],
        columnDefs: 
        [
            {
                targets: [0, 10],
                searchable: false,
                sortable: false
            },
            {
                targets: 9,
                render: function(data)
                {
                    var status = 
                    {
                        'A': 'Aktif Satışlar',
                        'L': 'Listelemeye Hazır Ürünler',
                        'S': 'Satılanlar',
                        'U': 'Satılmayanlar'
                    };
                    
                    return status[data] != undefined ? status[data] : data;
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

    @if (isset($datas->productCount) && $datas->productCount > $show)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == ceil($datas->productCount / $show))
            $('#page a:eq(1)').attr('go', '{{ ceil($datas->productCount / $show) }}');
        @elseif ($page > ceil($datas->productCount / $show))
            location.href = '/urunler/gittigidiyor?page={{ ceil($datas->productCount / $show) - 1 }}';
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

        var _input = $(this).closest('td').find('input, select').eq(0);

            _this = $(this), row = _input.closest('tr');

        if (row.attr('class').indexOf('child') != -1)
        {
            var index = $('#custom-datatable tr').index(row);

            row = $('#custom-datatable tr').eq(index - 1);
        }

        var datas = 
        {
            type: 'gittigidiyor',
            task: _input.attr('name'),
            value: _input.val(),
            id: row.attr('data-id'),
            item_id: row.attr('item-id')
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

        var _this = $(this), row = _this.closest('tr');

        if (row.attr('class').indexOf('child') != -1)
        {
            var index = $('#custom-datatable tr').index(row);

            row = $('#custom-datatable tr').eq(index - 1);
        }

        var datas = 
        {
            type: 'gittigidiyor',
            task: _this.attr('task'),
            id: row.attr('data-id'),
            item_id: row.attr('item-id'),
            stock: row.find('[name="stock"]').val()
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

    var _class = $('#menu-buttons a').eq(0).attr('class').replace('primary', 'secondary');

    $('#menu-buttons').find('[href="{{ $status }}"]').attr('class', _class);

    $('#menu-buttons a').not(':eq(0)').click(function(e)
    {
        e.preventDefault();

        $('[name="status"]').val($(this).attr('href'));

        $('.content form').trigger('submit');
    });

</script>

@endsection