@extends('product.list')

@section('title', 'Ürün - Fiyat')

@section('name', 'Fiyat')

@section('url', 'fiyat')

@section('content-list')

<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th rowspan="2">Fotoğraf</th>
            <th rowspan="2">İsim</th>
            <th rowspan="2">Stok Kodu</th>
            <th rowspan="2">Marka</th>
            <th colspan="2" class="text-center border-right border-left">Sistem</th>
            <th colspan="3" class="text-center border-right">Trendyol</th>
            <th colspan="3" class="text-center border-right">N11</th>
            <th colspan="3" class="text-center">Çiçeksepeti</th>
        </tr>
        <tr class="text-center">
            <th class="border-left">Fiyat</th>
            <th class="border-right">İndirimli Fiyat</th>
            <th>Fiyat</th>
            <th>İndirimli Fiyat</th>
            <th class="border-right">Satış</th>
            <th>Fiyat</th>
            <th>İndirimli Fiyat</th>
            <th class="border-right">Satış</th>
            <th>Fiyat</th>
            <th>İndirimli Fiyat</th>
            <th>Satış</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr data-id="{{ $data->id }}">
            <td>{{ $data->profile }}</td>
            <td>{{ $data->name }}</td>
            <td>{{ $data->code }}</td>
            <td>
                @if ($data->brand)
                {{ $data->brand }}
                @endif
            </td>
            <td data-order="{{ $data->price }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->price }}" name="price" data-id="{{ $data->id }}">
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
            <td data-order="{{ $data->discount }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->discount }}" name="discount" data-id="{{ $data->id }}">
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
            @foreach (['t', 'n', 'c'] as $type)
            @php $name = 'price_'. $type @endphp
            <td data-order="{{ $data->$name }}" class="border-left">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->$name }}" name="{{ $name }}" data-id="{{ $data->id }}">
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
            @php $name = 'discount_'. $type @endphp
            <td data-order="{{ $data->$name }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->$name }}" name="{{ $name }}" data-id="{{ $data->id }}">
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
            @php $name = $type == 't' ? 'stop' : 'stop_'. $type @endphp
            <td class="border-right">
                <button class="btn {{ $data->$name ? 'btn-success' : 'btn-warning' }} text-nowrap" 
                    task="{{ $data->$name ? 'start_'. $type : $name }}" data-id="{{ $data->id }}">
                    <text>{{ $data->$name ? 'Başlat' : 'Durdur' }}</text> <i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@section('script-list')

<style>

    #custom-datatable th,
    #custom-datatable td
    {
        display: table-cell !important;
    }

</style>

<script>

    $("#custom-datatable").DataTable(
    {
        order: [[2, "asc"]],
        columnDefs: 
        [
            {
                targets: [0],
                searchable: false,
                sortable: false,
            },
            {
                targets: 0,
                render: function(data) 
                {
                    if (data != '')
                    {
                        data = '/assets/images/products/'+ data;

                        return '<a href="'+ data +'" data-fancybox><img src="'+ data +'" width="50"></a>';
                    }

                    else return '<i class="uil uil-minus"></i>';
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
        ],
        responsive: 
        {
            details: false
        },
        /* 'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': 
        {
            'url':'ajaxfile.php'
        } */
    });

    $('body').on('click', 'button.input-group-text', function(e)
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
                type: 'system',
                task: _input.attr('name'),
                value: _input.val(),
                code: _input.attr('data-id')
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
                type: 'system',
                task: _this.attr('task'),
                id: _this.attr('data-id'),
            };

        _this.find('i').hide();

        _this.find('div').css('display', 'inline-block');

        $.get('/urunler/canli', datas, function(resp)
        {
            if (resp == true)
            {
                _this.find('i').attr('class', 'uil uil-check-circle ml-1');

                if (datas.task.indexOf('stop') != -1)
                {
                    _this.attr('class', _this.attr('class').replace('warning', 'success'));

                    _this.attr('task', datas.task == 'stop_n' ? 'start_n' : 'start');

                    _this.find('text').text('Başlat');
                }

                else
                {   
                    _this.attr('class', _this.attr('class').replace('success', 'warning'));

                    _this.attr('task', datas.task == 'start_n' ? 'stop_n' : 'stop');

                    _this.find('text').text('Durdur');
                }
            }

            else _this.find('i').attr('class', 'uil uil-exclamation-circle ml-1');

            _this.find('div').hide();

            _this.find('i').show();

        }).fail(function(a, b, error) 
        {
            _this.find('div').hide();

            _this.find('i').attr('class', 'uil uil-exclamation-circle ml-1').show();
        });
    });

</script>

@endsection