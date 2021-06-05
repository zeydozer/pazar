@extends('order.list')

@section('title', 'Sipariş - Trendyol')

@section('name', 'Trendyol')

@section('url', '/trendyol')

@section('content-list')

<?php

$date = Request::has('date') ? 

    explode(' | ', Request::get('date')) :

    [
        date('d.m.Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
        date('d.m.Y', strtotime('+1 days', strtotime(date('Y-m-d')))),
    ];

?>

<form class="mb-3">
    <input type="text" id="range-datepicker" class="form-control" 
        value="{{ $date[0] }} | {{ $date[1] }}" name="date">
    <input type="hidden" name="page" value="{{ $page }}">
</form>
@if (isset($datas->totalPages) && $datas->totalPages > 1)
<div class="row mb-3 align-items-center border-bottom pb-3" id="page">
    <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
        <a go="{{ $page - 1 }}"><i class="uil uil-angle-left"></i></a>
        <select name="page" class="custom-select mx-1 w-auto">
            @for ($i = 1; $i <= $datas->totalPages; $i++)
            
            <?php $selected = $page == $i ? 'selected' : '' ?>
            
            <option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
            @endfor
        </select>
        <a go="{{ $page + 1 }}"><i class="uil uil-angle-right"></i></a>
        <span class="ml-2">{{ $limit }} - {{ $limit + $show }}</span>
    </div>
    <div class="col-md-6 text-center text-md-right">
        <b>Toplam:</b> {{ $datas->totalElements }}
    </div>
</div>
@endif
<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Tarih</th>
            <th>No</th>
            <th>Fatura</th>
            <th>İsim</th>
            <th>Soyisim</th>
            <th>E-Posta</th>
            <th>İlçe</th>
            <th>İl</th>
            <th>Tutar</th>
            <th>Durum</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->content))
        @foreach ($datas->content as $data)
        <tr number="{{ $data->orderNumber }}">
            <td>
                <a href="/siparis/{{ $data->id }}">
                    <i class="uil uil-eye"></i>
                </a>
            </td>
            <td data-sort="{{ date('Y-m-d H:i:s', $data->orderDate / 1000) }}">
                {{ date('d.m.y H:i', $data->orderDate / 1000) }}
            </td>
            <td>{{ $data->orderNumber }}</td>
            
            <?php
                
            $invoice = \App\Models\Invoice::where('type', 'trendyol')
                ->where('order_id', $data->orderNumber)
                ->first();
            
            if (!$invoice) :

                $temp = 'kesilmedi';

            else :
            
                $temp = 'kesilmedi';

                if ($invoice->sales_id)

                    $temp = 'kesildi';

                if ($invoice->e_track)

                    $temp = 'resmileştirildi';

                if ($invoice->e_id)

                    $temp = 'resmi tamamlandı';

                if ($invoice->mail)

                    $temp = 'postalandı';

            endif;

            ?>
            
            <td data-sort="{{ $temp }}" data-search="{{ $temp }}">
                @if ($temp != 'postalandı')
                <button class="btn btn-success text-nowrap" data-id="{{ $data->orderNumber }}">
                    <text class="text-capitalize">{{ $temp }}</text><i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
                @else
                Postalandı <i class="uil uil-check ml-1"></i>
                @endif
            </td>
            <td>{{ $data->customerFirstName }}</td>
            <td>{{ $data->customerLastName }}</td>
            <td>{{ $data->customerEmail }}</td>
            <td>{{ $data->shipmentAddress->district }}</td>
            <td>{{ $data->shipmentAddress->city }}</td>
            <td data-sort="{{ $data->totalPrice }}">
                {{ $data->totalPrice }}₺
            </td>
            <td>{{ $data->shipmentPackageStatus }}</td>
        </tr>
        @endforeach
        @elseif (isset($datas->errors))
        <tr>
            <td colspan="100%" class="text-center">
                {{ $datas->errors[0]->message }}
            </td>
        </tr>
        @endif
    </tbody>
</table>

<div class="modal fade" id="order" tabindex="-1" role="dialog" 
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Sipariş</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="order-details" style="display: none">
                    ...
                </div>
                <div class="text-center mt-1" id="order-buttons">
                    <a href="#" class="btn btn-outline-primary btn-rounded width-md" data-dismiss="modal">
                        <i class="uil uil-arrow-left mr-1"></i> Geri
                    </a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@section('style-list')

<style>

    #order-info,
    #order-addresses,
    #histories
    {
        margin-bottom: -.5rem;
    }

    #histories div:not(:last-child)
    {
        border-bottom: 1px solid #ccc;
        margin-bottom: 1rem;
    }

    #order-info span,
    #order-addresses span,
    #histories span
    {
        display: flex;
        margin-bottom: .5rem;
    }

    #order-info span b:first-child,
    #order-addresses span b:first-child,
    #histories span b:first-child
    {
        display: block;
        width: 20;
        flex: 0 0 20%;
    }

    #order-info span b:last-child,
    #order-addresses span b:last-child,
    #histories span b:last-child
    {
        font-weight: normal;
    }

    #order-products th
    {
        white-space: nowrap;
    }

    #process .input-group
    {
        flex-wrap: nowrap;
    }

    #process .input-group .input-group-text
    {
        padding: .25rem .5rem;
        font-size: 12pt;
        line-height: 1;
    }

    #order-products .spinner-border,
    #process .spinner-border
    {
        width: 19px;
        height: 19px;
        border-width: 2px;
        display: none;
    }

    #order-products th,
    #order-products td
    {
        white-space: nowrap;
    }

    #order-products td [name^="quantity"]
    {
        min-width: 100px;
    }

    #order-products form
    {
        margin: 0 auto;
        margin-top: .75rem;
        padding-top: .75rem;
        width: 50%;
    }

    #range-datepicker
    {
        width: 200px;
    }

</style>

@endsection

@section('script-list')

<script>

    $("#custom-datatable").DataTable(
    {
        order: [[1, "desc"]],
        columnDefs: 
        [
            {
                targets: 0,
                sortable: false,
                searchable: false,
            },
            {
                targets: 6,
                render: function(data)
                {
                    return '<a href="mailto: '+ data +'">'+ data +'</a>';
                }
            },
            {
                targets: 10,
                render: function(data)
                {
                    var status =
                    {
                        'Awaiting': 'Bekliyor',
                        'Created': 'Hazır',
                        'Picking': 'Başladı',
                        'Invoiced': 'Fatura',
                        'Shipped': 'Taşıma',
                        'Cancelled': 'İptal',
                        'UnPacked': 'Bölündü',
                        'Delivered': 'Teslim',
                        'UnDelivered': 'Teslim Edilemedi',
                        'UnDeliveredAndReturned': 'Geri Dönüyor',
                        'ReadyToShip': 'Hazır'
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

    @if (isset($datas->totalPages) && $datas->totalPages > 1)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == $datas->totalPages)
            $('#page a:eq(1)').attr('go', '{{ $datas->totalPages }}');
        @elseif ($page > $datas->totalPages)
            location.href = '/siparisler/trendyol?page={{ $datas->totalPages - 1 }}&date={{ $date[0] }} | {{ $date[1] }}';
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

    $('body').on('click', '#custom-datatable a', function(e)
    {
        e.preventDefault();

        var datas = 
        {
            type: 'trendyol',
            task: 'detail',
            number: $(this).closest('tr').attr('number'),
            id: $(this).attr('href').split('/').pop()
        };

        $('#order-details').hide();

        $('#order .spinner-border').show();

        $('#order').modal('show');

        $.get('/siparisler/canli', datas, function(resp)
        {
            if (resp != false)
            {
                $('#order .spinner-border').hide();

                $('#order-details').html(resp).show();
            }

            else
            {
                $('#order .spinner-border').hide();

                $('#order-details').html('<i class="uil uil-exclamation-triangle mr-1"></i> Hata').show();
            }
        
        }).fail(function(a, b, error) 
        {
            $('#order .spinner-border').hide();

            $('#order-details').html('<i class="uil uil-exclamation-triangle mr-1"></i> '+ error).show();
        });
    });

    $('body').on('click', '#process button.input-group-text', function(e)
    {
        e.preventDefault();

        if ($(this).closest('.form-group').attr('id') == 'cargo')

            return false;

        var _input = $(this).closest('.input-group').find('input, select').eq(0);

            _this = $(this);

            datas = 
            {
                type: 'trendyol',
                task: _input.attr('name'),
                value: _input.val(),
                id: _input.closest('.tab-content').attr('data-id'),
                number: _input.closest('.tab-content').attr('number')
            };

        _this.find('i').hide();

        _this.find('div').show();
        
        $.get('/siparisler/canli', datas, function(resp)
        {
            if (resp === true)
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

    $('body').on('click', '#cargo button.input-group-text', function(e)
    {
        e.preventDefault();

        var _input = 
            [
                $(this).closest('.input-group').find('input, select').eq(0),
                $(this).closest('.input-group').find('input, select').eq(1)
            ];

            _this = $(this);

            datas = 
            {
                type: 'trendyol',
                task: 'box-deci',
                box: _input[0].val(),
                deci: _input[1].val(),
                id: _input[0].closest('.tab-content').attr('data-id'),
                number: _input[0].closest('.tab-content').attr('number')
            };

        _this.find('i').hide();

        _this.find('div').show();
        
        $.get('/siparisler/canli', datas, function(resp)
        {
            if (resp === true)
            {
                _input[0].attr('data-value', datas.box);

                _input[1].attr('data-value', datas.deci);

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

    var buttons = '#order [href="cancel"], #order [href="divide"], #order [href="claim"]';

    $('body').on('click', buttons, function(e)
    {
        e.preventDefault();

        var line = {}, barcode = {}, _this = $(this);

        $('#order-products [type="checkbox"]').each(function()
        {
            if ($(this).is(':checked'))
            {
                line[$(this).val()] = $('[name="quantity['+ $(this).val() +']"]').val();

                barcode[$(this).val()] = $(this).closest('tr').find('td:eq(3)').text();
            }
        });

        var datas = 
        {
            type: 'trendyol',
            task: _this.attr('href'),
            line: line,
            id: _this.closest('.tab-content').attr('data-id'),
            number: _this.closest('.tab-content').attr('number'),
            barcode: barcode,
            customer: _this.closest('.tab-content').attr('customer-id'),
        };

        if (datas.task == 'claim')
        
            datas['cargo'] = _this.closest('form').find('select').val();

        _this.find('text').hide();

        _this.find('div').show();
        
        $.get('/siparisler/canli', datas, function(resp)
        {
            if (resp === true)
            {
                _this.find('i').attr('class', 'uil uil-check');

                setTimeout(function()
                {
                    location.reload();
                
                }, 2000);
            }

            else _this.find('i').attr('class', 'uil uil-exclamation-circle');

            _this.find('div').hide();

            _this.find('text').show();
        
        }).fail(function(a, b, error) 
        {
            _this.find('div').hide();

            _this.find('i').attr('class', 'uil uil-exclamation-circle');

            _this.find('text').show();
        });
    });

    $('body').on('change', '#range-datepicker', function()
    {
        if ($(this).val().split(' | ').length > 1)

            $(this).closest('form').submit();
    });

    $('body').on('click', '#custom-datatable button.btn', function(e)
    {
        e.preventDefault();

        var _this = $(this);

            datas = 
            {
                type: 'trendyol',
                id: _this.attr('data-id'),
            };

        _this.find('i').hide();

        _this.find('div').css('display', 'inline-block');

        $.get('/siparisler/fatura', datas, function(resp)
        {
            if (resp[0] == true)
            
                _this.find('i').attr('class', 'uil uil-check-circle ml-1');

            else
            {
                _this.find('i').attr('class', 'uil uil-exclamation-circle ml-1');

                result(resp[0], resp[1]);
            }

            _this.find('div').hide();

            _this.find('i').show();

            datas.task = 'invoice-s';

            $.get('/siparisler/canli', datas, function(resp)
            {
                _this.find('text').text(resp);
            });

        }).fail(function(a, b, error) 
        {
            _this.find('div').hide();

            _this.find('i').attr('class', 'uil uil-exclamation-circle ml-1').show();

            result('error', error);
        });
    });

</script>

@endsection