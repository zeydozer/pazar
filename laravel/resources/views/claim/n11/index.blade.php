@extends('claim.list')

@section('name', 'N11')

@section('content-list')

<?php

$date = Request::has('date') ? 

    explode(' | ', Request::get('date')) :

    [
        date('d.m.Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
        date('d.m.Y'),
    ];

?>

<form class="row mb-3" id="menu-buttons">
    <div class="col-md-3">
        <input type="text" id="range-datepicker" class="form-control" 
            value="{{ $date[0] }} | {{ $date[1] }}" name="date">
        <input type="hidden" name="page" value="{{ $page }}">
    </div>
    <div class="col-md-2 offset-md-3">
        <a href="/iadeler/n11" class="btn btn-primary btn-block">
            <i class="uil uil-backward mr-1"></i> İade
        </a>
    </div>
    <div class="col-md-2">
        <a href="/iadeler/n11/iptal" class="btn btn-primary btn-block">
            <i class="uil uil-times mr-1"></i> İptal
        </a>
    </div>
    <div class="col-md-2">
        <a href="/iadeler/n11/degisim" class="btn btn-primary btn-block">
            <i class="uil uil-sync mr-1"></i> Değişim
        </a>
    </div>
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

@yield('content-claim')

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

@yield('script-claim')

<script>

    var path = '{{ Request::path() }}';

        _class = $('#menu-buttons a').eq(0).attr('class').replace('primary', 'secondary');

    if (path.indexOf('degisim') != -1)

        $('#menu-buttons a').eq(2).attr('class', _class);

    else if (path.indexOf('iptal') != -1)

        $('#menu-buttons a').eq(1).attr('class', _class);

    else $('#menu-buttons a').eq(0).attr('class', _class);

</script>

@endsection