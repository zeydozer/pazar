@extends('bill.list')

@section('title', 'Hesaplar - N11')

@section('name', 'N11')

@section('url', '/n11')

@section('content-list')

<?php

$date = Request::has('date') ? 

    explode(' | ', Request::get('date')) :

    [
        date('d.m.Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
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
            <th>Tarih</th>
            <th>No</th>
            <th>Tip</th>
            <th>Borç</th>
            <th>Alacak</th>
            <th>Bakiye</th>
            <th>Birim</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>

        <?php $total = ['alacak' => 0, 'borç' => 0, 'bakiye' => 0] ?>

        @if (isset($datas->bankStatementInvoiceDataList->bankStatementInvoiceItemData))

        <?php 
        
        if (!is_array($datas->bankStatementInvoiceDataList->bankStatementInvoiceItemData))
        {
            $datas->bankStatementInvoiceDataList->bankStatementInvoiceItemData = 
            [
                $datas->bankStatementInvoiceDataList->bankStatementInvoiceItemData
            ];
        }
        
        ?>

        @foreach ($datas->bankStatementInvoiceDataList->bankStatementInvoiceItemData as $data)
        <tr>

            <?php
            
            list($day, $month, $year) = explode('.', $data->BUDAT);

            $temp = $year .'-'. $month .'-'. $day;
            
            ?>

            <td data-sort="{{ $temp }}">            
                {{ $data->BUDAT }}
            </td>
            <td>{{ $data->BELNR }}</td>
            <td>{{ $data->BLART_LTEXT }}</td>
            <td data-sort="{{ floatval($data->BORC) }}">

                <?php $total['borç'] += floatval($data->BORC) ?>

                {{ number_format(floatval($data->BORC), 2) }}
            </td>
            <td data-sort="{{ floatval($data->ALACAK) }}">

                <?php $total['alacak'] += floatval($data->ALACAK) ?>

                {{ number_format(floatval($data->ALACAK), 2) }}
            </td>
            <td data-sort="{{ floatval($data->NBAKIYE) }}">

                <?php $total['bakiye'] += floatval($data->NBAKIYE) ?>

                {{ number_format(floatval($data->NBAKIYE), 2) }}
            </td>
            <td>{{ $data->SGTXT }}</td>
            <td>{{ $data->WAERS }}</td>
        </tr>
        @endforeach
        @elseif (isset($datas->result->errorMessage))
        <tr>
            <td colspan="100%" class="text-center">
                {{ $datas->result->errorMessage }}
            </td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Toplam</th>
            <th>{{ number_format($total['borç'], 2) }}₺</th>
            <th>{{ number_format($total['alacak'], 2) }}₺</th>
            <th>{{ number_format($total['bakiye'], 2) }}₺</th>
            <th colspan="2"></th>
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

</style>

@endsection

@section('script-list')

<script>

    @if (isset($datas->bankStatementInvoiceDataList->bankStatementInvoiceItemData))
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
            var total = {1:0, 2:0, 3:0}, data = table.rows({search: 'applied'}).data();

            $.each(data, function(j)
            {
                for (var i = 3; i <= 5; i++)
                {
                    var value = Number(data[j][i]);

                    total[i - 2] += value;
                }
            });

            $.each(total, function(j)
            {
                value = total[j].toFixed(2);

                $('#custom-datatable tfoot th').eq(j).html(value);
            });

        }, 500));
    @endif

    @if (isset($datas->pagingData->pageCount) && $datas->pagingData->pageCount > 1)
        @if ($page == 1)
            $('#page a:eq(0)').attr('go', 1);
        @elseif ($page == $datas->pagingData->pageCount)
            $('#page a:eq(1)').attr('go', '{{ $datas->pagingData->pageCount }}');
        @elseif ($page > $datas->pagingData->pageCount)
            location.href = '/hesaplar/n11?page={{ $datas->pagingData->pageCount - 1 }}&date={{ $date[0] }} | {{ $date[1] }}';
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

</script>

@endsection