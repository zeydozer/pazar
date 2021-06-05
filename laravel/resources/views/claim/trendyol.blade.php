@extends('claim.list')

@section('title', 'İade - Trendyol')

@section('name', 'Trendyol')

@section('url', '/trendyol')

@section('content-list')

@if (isset($datas->totalPages) && $datas->totalPages > 1)
<form class="row mb-3 align-items-center border-bottom pb-3" id="page">
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
</form>
@endif
<table id="custom-datatable" class="table dt-responsive nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Tarih</th>
            <th>İade</th>
            <th>No</th>
            <th>İsim</th>
            <th>Soyisim</th>
            <th>Kargo</th>
            <th>Kargo No</th>
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
            @if (isset($data->claimDate))
            <td data-sort="{{ date('Y-m-d H:i:s', $data->claimDate / 1000) }}">
                {{ date('d.m.y H:i', $data->claimDate / 1000) }}
            </td>
            @else
            <td></td>
            @endif
            <td>{{ $data->orderNumber }}</td>
            <td>{{ $data->customerFirstName }}</td>
            <td>{{ $data->customerLastName }}</td>
            @if (isset($data->cargoProviderName))
            <td>{{ $data->cargoProviderName }}</td>
            @else
            <td></td>
            @endif
            @if (isset($data->cargoTrackingNumber))
            <td>{{ $data->cargoTrackingNumber }}</td>
            @else
            <td></td>
            @endif

            <?php 
            
            $amount = 0;

            $status = [];

            foreach ($data->items as $item) :

                $amount += $item->orderLine->price;

                foreach ($item->claimItems as $claim)

                    $status[] = $claim->claimItemStatus->name;
            
            endforeach;

            // $status = implode(', ', array_unique(array_filter($status)));

            ?>

            <td data-sort="{{ $amount }}">{{ $amount }}₺</td>
            <td>{{ implode(', ', $status) }}</td>
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
                <h5 class="modal-title" id="myExtraLargeModalLabel">İade</h5>
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

    #order-info
    {
        margin-bottom: -.5rem;
    }

    #order-info span
    {
        display: flex;
        margin-bottom: .5rem;
    }

    #order-info span b:first-child
    {
        display: block;
        width: 20;
        flex: 0 0 20%;
    }

    #order-info span b:last-child
    {
        font-weight: normal;
    }

    #order-products th,
    #order-products td
    {
        white-space: nowrap;
    }

    #order-products .spinner-border
    {
        width: 19px;
        height: 19px;
        border-width: 2px;
        display: none;
    }

    #order-products form
    {
        margin: 0 auto;
        margin-top: .75rem;
        padding-top: .75rem;
        width: 50%;
    }

</style>

@endsection

@section('script-list')

<script>

    $("#custom-datatable").DataTable(
    {
        order: [[2, "desc"]],
        columnDefs: 
        [
            {
                targets: 0,
                sortable: false,
                searchable: false,
            },
            {
                targets: 9,
                render: function(data)
                {
                    var status =
                    {
                        'Accepted': 'Onaylandı',
                        'Created': 'Hazır',
                        'WaitingInAction': 'Bekliyor',
                        'Unresolved': 'İhtilaflı',
                        'Rejected': 'Reddedildi',
                        'Cancelled': '7 Gün Geçti',
                        'InAnalysis': 'Analizde'
                    };

                    var temp = [];

                    $.each(data.split(', '), function(i, j)
                    {
                        temp[i] = status[j] != undefined ? status[j] : j;
                    });

                    return temp.join(', ');
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
            location.href = '/iadeler/trendyol?page={{ $datas->totalPages - 1 }}';
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
            task: 'detail-r',
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

    $('body').on('click', '#order [href="approve"]', function(e)
    {
        e.preventDefault();

        var line = [], _this = $(this);

        $('#order-products [type="checkbox"]').each(function(i)
        {
            if ($(this).is(':checked'))

                line[i] = $(this).val();
        });

        var datas = 
        {
            type: 'trendyol',
            task: _this.attr('href'),
            line: line,
            id: _this.closest('.tab-content').attr('data-id'),
            number: _this.closest('.tab-content').attr('number')
        };

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

    $('body').on('submit', '#issue', function(e)
    {
        e.preventDefault();

        var data = new FormData(this), 
                    
            form = $(this),
            
            button = $(this).find('button');
        
        data.append('_token', '{{ csrf_token() }}');

        data.append('type', 'trendyol');

        data.append('task', 'issue');

        $('#order-products [type="checkbox"]').each(function(i)
        {
            if ($(this).is(':checked'))

                data.append('line[]', $(this).val());
        });

        data.append('id', form.closest('.tab-content').attr('data-id'));

        $.ajax(
        {
            url: '/siparisler/canli',
            type: 'post',
            data: data,
            processData: false,
            contentType: false,
            beforeSend: function() 
            {
                button.find('text').hide();

                button.find('div').show();

                form.find('*').attr('disabled', true);
            },
            success: function(resp)
            {
                if (resp === true)
                
                    button.find('i').attr('class', 'uil uil-check');

                else button.find('i').attr('class', 'uil uil-exclamation-circle');

                button.find('div').hide();

                button.find('text').show();

                form.find('*').removeAttr('disabled');
            },
            error: function(a, b, error)
            {
                form.find('*').removeAttr('disabled');

                button.find('div').hide();

                button.find('i').attr('class', 'uil uil-exclamation-circle');

                button.find('text').show();
            }
        });
    });

</script>

@endsection