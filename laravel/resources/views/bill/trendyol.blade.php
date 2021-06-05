@extends('bill.list')

@section('title', 'Hesaplar - Trendyol')

@section('name', 'Trendyol')

@section('url', '/trendyol')

@section('content-list')

<?php

$date = Request::has('date') ? 

    explode(' | ', Request::get('date')) :

    [
        date('d.m.Y', strtotime('-10 day', strtotime(date('Y-m-d')))),
        date('d.m.Y', strtotime('+1 day', strtotime(date('Y-m-d')))),
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
            <th>Tarih</th>
            <th>No</th>
            <th>Sipariş</th>
            <th>Fatura</th>
            <th>İsim</th>
            <th>Soyisim</th>
            <th>Tutar</th>
            <th>Komisyon</th>
            <th>Oran</th>
            <th>Gelir</th>
            <th>İşlem</th>
            <th>Durum</th>
            <th>Ürün</th>
            <th>Barkod</th>
        </tr>
    </thead>
    <tbody>

        <?php $total = ['price' => 0, 'commission' => 0, 'supplierRevenue' => 0] ?>

        @if (isset($datas->content) && count($datas->content) > 0)
        @foreach ($datas->content[0]->settlementItems as $data)
        <tr>
            <td data-sort="{{ date('Y-m-d H:i:s', $data->operationDate / 1000) }}" 
                class="text-nowrap">
                {{ date('d.m.y H:i', $data->operationDate / 1000) }}
            </td>
            <td>{{ $data->transactionId }}</td>
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
            <td>{{ $data->firstName }}</td>
            <td>{{ $data->lastName }}</td>
            <td data-sort="{{ $data->price }}">

                <?php
                
                if ($data->transactionTypeName != 'Sale')

                    $total['price'] -= $data->price;

                else $total['price'] += $data->price;
                
                ?>

                {{ number_format($data->price, 2) }}₺
            </td>
            <td data-sort="{{ $data->commission }}">

                <?php
                
                if ($data->transactionTypeName != 'Sale')

                    $total['commission'] -= $data->commission;

                else $total['commission'] += $data->commission;
                
                ?>

                {{ number_format($data->commission, 2) }}₺
            </td>
            <td data-sort="{{ $data->commissionRate }}">
                %{{ $data->commissionRate }}
            </td>
            <td data-sort="{{ $data->supplierRevenue }}">

                <?php
                
                if ($data->transactionTypeName != 'Sale')

                    $total['supplierRevenue'] -= $data->supplierRevenue;

                else $total['supplierRevenue'] += $data->supplierRevenue;
                
                ?>

                {{ number_format($data->supplierRevenue, 2) }}₺
            </td>
            <td>{{ $data->transactionTypeName }}</td>
            <td>{{ $data->settlementItemStatusName }}</td>
            <td>{{ $data->productName }}</td>
            <td>{{ $data->barcode }}</td>
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
    <tfoot>
        <tr>
            <th colspan="6">Toplam</th>
            <th>{{ number_format($total['price'], 2) }}₺</th>
            <th>{{ number_format($total['commission'], 2) }}₺</th>
            <th></th>
            <th>{{ number_format($total['supplierRevenue'], 2) }}₺</th>
            <th colspan="4"></th>
        </tr>
    </tfoot>
</table>

@endsection

@section('style-list')

<style>

    #range-datepicker
    {
        width: 200px;
    }

    .btn .spinner-border
    {
        width: 19px;
        height: 19px;
        border-width: 2px;
        display: none;
    }

</style>

@endsection

@section('script-list')

<script>

    @if (!isset($datas->errors))
        function delay(callback, ms) 
        {
            var timer = 0;
            
            return function() 
            {
                var context = this, args = arguments;
                
                clearTimeout(timer);
                
                timer = setTimeout(function() 
                {
                    callback.apply(context, args);
                
                }, ms || 0);
            };
        }
        
        var table = $("#custom-datatable").DataTable(
        {
            order: [[0, "desc"]],
            columnDefs: 
            [
                {
                    targets: 10,
                    render: function(data)
                    {
                        var status =
                        {
                            'Sale': 'Satış',
                            'Return': 'İade',
                            'Cancel': 'İptal',
                            'Discount': 'İndirim',
                            'DiscountCancel': 'İndirim İptal',
                            'Coupon': 'Kupon',
                            'CouponCancel': 'Kupon İptal',
                        };

                        return status[data] != undefined ? status[data] : data;
                    }
                },
                {
                    targets: 11,
                    render: function(data)
                    {
                        var status =
                        {
                            'Paid': 'Ödendi',
                            'UnPaid': 'Ödenmedi',
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

        table.on('search.dt', delay(function() 
        {
            var total = {1:0, 2:0, 4:0}, data = table.rows({search: 'applied'}).data();

            $.each(data, function(j)
            {
                for (var i = 6; i <= 9; i++)
                {
                    if (i == 8)

                        continue;

                    var value = Number(data[j][i].display.replace('₺', ''));

                    if (data[j][9] == 'Sale')
                    
                        total[i - 4] += value;

                    else total[i - 4] -= value;
                }
            });

            $.each(total, function(j)
            {
                value = total[j].toFixed(2) +'₺';

                $('#custom-datatable tfoot th').eq(j).html(value);
            });

        }, 500));
    @endif

    @if (isset($datas->totalPages) && $datas->totalPages > 1)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == $datas->totalPages)
            $('#page a:eq(1)').attr('go', '{{ $datas->totalPages }}');
        @elseif ($page > $datas->totalPages)
            location.href = '/hesaplar/trendyol?page={{ $datas->totalPages - 1 }}&date={{ $date[0] }} | {{ $date[1] }}';
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