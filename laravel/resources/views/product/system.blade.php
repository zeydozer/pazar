@extends('product.list')

@section('title', 'Ürün - Sistem')

@section('name', 'Sistem')

@section('url', null)

@section('content-list')

<a href="/urun" class="btn btn-primary mb-3">
    <i class="uil uil-plus-circle"></i> Ürün Ekle
</a>

<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Fotoğraf</th>
            <th>İsim</th>
            <th>Stok Kodu</th>
            <th>Ana Kategori</th>
            <th>Marka</th>
            <!-- <th>Alış Fiyatı</th> -->
            <th>Fiyat</th>
            <th>İndirimli Fiyat</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr data-id="{{ $data->id }}">
            <td>
                <a href="/urun/{{ $data->id }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>
            <td>
                @if ($data->profile)
                {{ $data->profile->name }}
                @endif
            </td>
            <td>{{ $data->name }}</td>
            <td>{{ $data->code }}</td>
            <td>
                @if ($data->category)
                {{ $categories[$data->category->id] }}
                @endif
            </td>
            <td>
                @if ($data->brand)
                {{ $brands[$data->brand->id] }}
                @endif
            </td>
            <!-- <td></td> -->
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
            <td data-order="{{ $data->discount }}">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="0.00" step="1.00"
                        value="{{ $data->discount }}" name="discount">
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
            <td data-order="{{ $data->stock }}">
                <!-- <div class="input-group">
                    <input type="number" class="form-control" placeholder="0" step="1"
                        value="{{ $data->stock }}" name="stock">
                    <div class="input-group-append">
                        <button class="input-group-text">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                </div> -->
                {{ $data->stock }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@section('script-list')

<script>

    $("#custom-datatable").DataTable(
    {
        order: [[2, "asc"]],
        columnDefs: 
        [
            {
                targets: [0, 1],
                searchable: false,
                sortable: false,
            },
            {
                targets: 1,
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
        ]
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
                code: _input.closest('tr').attr('data-id')
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

</script>

@endsection