@extends('order.list')

@section('title', 'Sipariş - N11')

@section('name', 'N11')

@section('url', '/n11')

@section('content-list')

<?php

$date = Request::has('date') ? 

    explode(' | ', Request::get('date')) :

    [
        date('d.m.Y', strtotime('-2 year', strtotime(date('Y-m-d')))),
        date('d.m.Y'),
    ];

?>

<form class="mb-3">
    <input type="text" id="range-datepicker" class="form-control" 
        value="{{ $date[0] }} | {{ $date[1] }}" name="date">
    <input type="hidden" name="page" value="{{ $page }}">
</form>
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
            <th>Tarih</th>
            <th>No</th>
            <th>Fatura</th>
            <th>Tc Kimlik No</th>
            <th>Ödeme</th>
            <th>Tutar</th>
            <th>Durum</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->orderList->order))

        <?php 
        
        if (!is_array($datas->orderList->order)) 

            $datas->orderList->order = [$datas->orderList->order];
        
        ?>

        @foreach ($datas->orderList->order as $data)
        <tr number="{{ $data->orderNumber }}">
            <td>
                <a href="/siparis/{{ $data->id }}">
                    <i class="uil uil-eye"></i>
                </a>
            </td>

            <?php
            
            $temp = explode(' ', $data->createDate);

            list($day, $month, $year) = explode('/', $temp[0]);

            $temp = $year .'-'. $month .'-'. $day .' '. $temp[1];
            
            ?>


            <td data-sort="{{ $temp }}">
                {{ str_replace('/', '.', $data->createDate) }}
            </td>
            <td>{{ $data->orderNumber }}</td>

            <?php
                
            $invoice = \App\Models\Invoice::where('type', 'n11')
                ->where('order_id', $data->id)
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
                <button class="btn btn-success text-nowrap" data-id="{{ $data->id }}">
                    <text class="text-capitalize">{{ $temp }}</text><i class="uil uil-refresh ml-1"></i>
                    <div class="spinner-border text-white ml-1" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                </button>
                @else
                Postalandı <i class="uil uil-check ml-1"></i>
                @endif
            </td>
            <td>{{ $data->citizenshipId }}</td>
            <td>{{ $data->paymentType }}</td>
            <td data-sort="{{ $data->totalAmount }}">
                {{ $data->totalAmount }}₺
            </td>
            <td>{{ $data->status }}</td>
        </tr>
        @endforeach
        @elseif (isset($datas->errors))
        <tr>
            <td colspan="100%" class="text-center">
                {{ $datas->result->errorMessage }}
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
                targets: 5,
                render: function(data)
                {
                    var status = ['Kredi Kartı', 'BKMEXPRESS', 'AKBANKDIREKT', 'PAYPAL', 'MallPoint', 
                        'GARANTIPAY', 'GarantiLoan', 'MasterPass', 'ISBANKPAY', 'PAYCELL', 'COMPAY', 
                        'YKBPAY', 'Diğer'];

                    return status[data - 1] != undefined ? status[data - 1] : data;
                }
            },
            {
                targets: 7,
                render: function(data)
                {
                    var status = ['İşlem Bekliyor', 'İşlemde', 'İptal Edilmiş', 'Geçersiz', 'Tamamlandı'];

                    return status[data - 1] != undefined ? status[data - 1] : data;
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
            location.href = '/siparisler/n11?page={{ $datas->pagingData->pageCount - 1 }}&date={{ $date[0] }} | {{ $date[1] }}';
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
            type: 'n11',
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

    $('body').on('change', '#range-datepicker', function()
    {
        if ($(this).val().split(' | ').length > 1)

            $(this).closest('form').submit();
    });

    var buttons = '#order [href="accept"], #order [href="reject"], #order [href="cargo"]';

    $('body').on('click', buttons, function(e)
    {
        e.preventDefault();

        var ids = {}, _this = $(this);

        $('#order-products [type="checkbox"]').each(function(i)
        {
            if ($(this).is(':checked'))
            
                ids[i] = $(this).val();
        });

        var datas = 
        {
            type: 'n11',
            task: _this.attr('href'),
            ids: ids
        };

        _this.closest('form').find('input, select').each(function()
        {
            datas[$(this).attr('name')] = $(this).val();
        });

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

    $('body').on('click', '#custom-datatable button.btn', function(e)
    {
        e.preventDefault();

        var _this = $(this);

            datas = 
            {
                type: 'n11',
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