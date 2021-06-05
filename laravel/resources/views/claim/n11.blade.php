@extends('claim.list')

@section('title', 'İade - N11')

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
            <th></th>
            <th>Ödeme Tarihi</th>
            <th>Talep Tarihi</th>
            <th>İsim</th>
            <th>E-Posta</th>
            <th>Telefon</th>
            <th>Sipariş No</th>
            <th>Ürün</th>
            <th>Adet</th>
            <th>Tutar</th>
            <th>Kargo</th>
            <th>Firma</th>
            <th>Durum</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($datas->claimCancelList->claimCancel))

        <?php 
        
        if (!is_array($datas->claimCancelList->claimCancel)) 

            $datas->claimCancelList->claimCancel = [$datas->claimCancelList->claimCancel];
        
        ?>

        @foreach ($datas->claimCancelList->claimCancel as $data)
        <tr number="{{ $data->orderNumber }}">
            <td>
                <a href="/siparis/{{ $data->claimCancelId }}">
                    <i class="uil uil-edit"></i>
                </a>
            </td>

            <?php
            
            list($day, $month, $year) = explode('/', $data->paymentDate);

            $temp = $year .'-'. $month .'-'. $day;
            
            ?>


            <td data-sort="{{ $temp }}">
                {{ str_replace('/', '.', $data->paymentDate) }}
            </td>

            <?php
            
            list($day, $month, $year) = explode('/', $data->requestDate);

            $temp = $year .'-'. $month .'-'. $day;
            
            ?>


            <td data-sort="{{ $temp }}">
                {{ str_replace('/', '.', $data->requestDate) }}
            </td>
            <td>{{ $data->buyerName }}</td>
            <td>{{ $data->buyerEmail }}</td>
            <td>{{ $data->buyerPhone }}</td>
            <td>{{ $data->orderNumber }}</td>
            <td>{{ $data->productName }}</td>
            <td>{{ $data->quantity }}</td>
            <td data-sort="{{ $data->finalPrice }}">
                {{ $data->finalPrice }}₺
            </td>
            <td>{{ $data->deliveryFeeType }}</td>
            <td>{{ $data->shipmentCompany }}</td>
            <td>
                @if (!is_object($data->status))
                {{ $data->status }}
                @endif
            </td>
        </tr>
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

<div class="modal fade" id="order" tabindex="-1" role="dialog" 
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">İade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="order-details">
                    <input type="hidden" name="id" value="">
                    <div class="text-center" id="order-products">
                        <div class="row w-50 m-auto">
                            <div class="col">
                                <a href="approve" class="btn btn-secondary btn-block">
                                    <text><i class="uil uil-check mr-1"></i> Onayla</text>
                                    <div class="spinner-border text-white" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <form id="issue" class="border-top row">
                            <div class="col-md-12 mb-2">
                                <input type="text" name="description" class="form-control" placeholder="Açıklama">
                            </div>
                            <div class="col-md-8">
                                <select name="reason" class="form-control">
                                    <option value="" selected disabled>- Red Nedeni</option>
                                    @foreach ($reasons as $reason)
                                    @if (count((array) $reason) > 0)
                                    <option value="{{ $reason->id }}">{{ $reason->value }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <a href="deny" class="btn btn-secondary btn-block">
                                    <text><i class="uil uil-cancel mr-1"></i> Reddet</text>
                                    <div class="spinner-border text-white" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-4" id="order-buttons">
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
        order: [[2, 'desc']],
        columnDefs: 
        [
            {
                targets: 0,
                sortable: false,
                searchable: false,
            },
            {
                targets: 4,
                render: function(data)
                {
                    return '<a href="mailto: '+ data +'">'+ data +'</a>';
                }
            },
            {
                targets: 5,
                render: function(data)
                {
                    return '<a href="tel: +90'+ data +'">'+ data +'</a>';
                }
            },
            {
                targets: 10,
                render: function(data)
                {
                    var status =
                    {
                        'ByBuyer': 'Alıcı Öder',
                        'BySeller': 'Mağaza Öder',
                        'Conditional': 'Şartlı Kargo',
                        '': '<i class="uil uil-minus"></i>'
                    }

                    return status[data] != undefined ? status[data] : data;
                }
            },
            {
                targets: 12,
                render: function(data)
                {
                    var status = 
                    {
                        'REQUESTED': 'İptal Talebi Geldi', 
                        'RETRACTED': 'İptal Geri Çekildi', 
                        'COMPLETED': 'İptal Edildi', 
                        'DENIED': 'İptaş Talebi Reddedildi', 
                        'REJECT': 'Reddedildi', 
                        'MANUAL_REFUND': 'Manuel Para İadesi Tamamlandı',
                        '': '<i class="uil uil-minus"></i>'
                    };

                    return status[data] != undefined ? status[data] : data;
                }
            },
            {
                targets: '_all',
                render: function(data)
                {
                    return $.trim(data) == '' || data == '0' ? 
                    
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
            location.href = '/iadeler/n11?page={{ $datas->pagingData->pageCount - 1 }}&date={{ $date[0] }} | {{ $date[1] }}';
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

    $('body').on('click', '#custom-datatable [href^="/siparis"]', function(e)
    {
        e.preventDefault();

        var id = $(this).attr('href').split('/').pop();

        $('#order-details [name="id"]').val(id);

        $('#order .spinner-border').show();

        $('#order').modal('show');

        $('#order .spinner-border').hide();
    });

    var buttons = '#order [href="approve"], #order [href="deny"]';

    $('body').on('click', buttons, function(e)
    {
        e.preventDefault();

        var _this = $(this);

            datas = 
            {
                type: 'n11',
                task: _this.attr('href'),
                id: $('#order-details [name="id"]').val()
            };

        if (datas.task == 'deny')
        {
            datas['reason'] = $('#order-details [name="reason"]').val();

            datas['description'] = $('#order-details [name="description"]').val();
        }

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

</script>

@endsection