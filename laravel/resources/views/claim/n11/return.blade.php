@extends('claim.n11.index')

@section('title', 'İade - N11')

@section('url', '/n11')

@section('content-claim')

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
        @if (isset($datas->claimReturnList->claimReturn))

        <?php 
        
        if (!is_array($datas->claimReturnList->claimReturn)) 

            $datas->claimReturnList->claimReturn = [$datas->claimReturnList->claimReturn];
        
        ?>

        @foreach ($datas->claimReturnList->claimReturn as $data)
        <tr number="{{ $data->orderNumber }}">
            <td>
                <a href="/siparis/{{ $data->claimReturnId }}">
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
        @elseif (isset($datas->result->errorMessage))
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
                                    @if (isset($reasons))
                                    @foreach ($reasons as $reason)
                                    @if (count((array) $reason) > 0)
                                    <option value="{{ $reason->id }}">{{ $reason->value }}</option>
                                    @endif
                                    @endforeach
                                    @endif
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
                        <form id="issue-p" class="border-top row">
                            <div class="col-md-12 mb-2">
                                <input type="text" name="description" class="form-control" placeholder="Açıklama">
                            </div>
                            <div class="col-md-5">
                                <select name="reason" class="form-control">
                                    <option value="" selected disabled>- Red Nedeni</option>
                                    @if (isset($pending))
                                    @foreach ($pending as $reason)
                                    @if (count((array) $reason) > 0)
                                    <option value="{{ $reason->id }}">{{ $reason->value }}</option>
                                    @endif
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="day" class="form-control" placeholder="Gün Sayısı">
                            </div>
                            <div class="col-md-4">
                                <a href="pend" class="btn btn-secondary btn-block">
                                    <text><i class="uil uil-clock mr-1"></i> Ertele</text>
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

@section('script-claim')

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
                        'REQUESTED': 'İade Talebi Geldi',
                        'CANCELLED': 'İptal Edildi',
                        'DENIED': 'Reddedildi', 
                        'PENDING': 'Erteleme Talep Edildi',
                        'PENDED': 'Ertelendi',
                        'APPROVED': 'Onaylandı',
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

    var buttons = '#order [href="approve"], #order [href="deny"], #order [href="pend"]';

    $('body').on('click', buttons, function(e)
    {
        e.preventDefault();

        var _this = $(this);

            datas = 
            {
                type: 'n11',
                task: _this.attr('href') +'-r',
                id: $('#order-details [name="id"]').val()
            };

        if (datas.task != 'approve-r')
        {
            datas['reason'] = $('#order-details [name="reason"]').val();

            datas['description'] = $('#order-details [name="description"]').val();

            if (datas.task == 'pend-r')

                datas['day'] = $('#order-details [name="day"]').val();
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