<?php

$status =
[
    'Accepted' => 'Onaylandı',
    'Created' => 'Hazır',
    'WaitingInAction' => 'Bekliyor',
    'Unresolved' => 'İhtilaflı',
    'Rejected' => 'Reddedildi',
    'Cancelled' => '7 Gün Geçti',
    'InAnalysis' => 'Analizde'
];

?>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a href="#order-info" data-toggle="tab" aria-expanded="false"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-info-circle"></i></span>
            <span class="d-none d-sm-block">Bilgi</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#order-products" data-toggle="tab" aria-expanded="true"
            class="nav-link active">
            <span class="d-block d-sm-none"><i class="uil-cart"></i></span>
            <span class="d-none d-sm-block">Ürün</span>
        </a>
    </li>
</ul>
<div class="tab-content p-3 text-muted" data-id="{{ $order->id }}" number="{{ $order->orderNumber }}">
    <div class="tab-pane" id="order-info">
        <span><b>Tarih:</b><b>{{ date('d.m.Y H:i:s', $order->orderDate / 1000) }}</b></span>
        <span><b>İade:</b><b>{{ date('d.m.Y H:i:s', $order->claimDate / 1000) }}</b></span>
        <span><b>No:</b><b>{{ $order->orderNumber }}</b></span>
        <span><b>İsim:</b><b>{{ $order->customerFirstName }}</b></span>
        <span><b>Soyisim:</b><b>{{ $order->customerLastName }}</b></span>
        <span><b>Kargo:</b><b>{{ $order->cargoProviderName }}</b></span>
        @if (isset($order->cargoSenderNumber) && $order->cargoSenderNumber)
        <span><b>Kargo No:</b><b>{{ $order->cargoSenderNumber }}</b></span>
        @endif
        
        <?php 
            
        $amount = 0;

        foreach ($order->items as $item)

            $amount += $item->orderLine->price

        ?>

        <span><b>Tutar:</b><b>{{ $amount }}₺</b></span>
    </div>
    <div class="tab-pane show active" id="order-products">
        <div class="table-responsive mb-3">
            <table class="table m-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>İsim</th>
                        <th>Stok Kodu</th>
                        <th>Barkod</th>
                        <th>Ebat</th>
                        <th>Tutar</th>
                        <th>Müşteri</th>
                        <th>Trendyol</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $i => $item)
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="pro-check-{{ $i }}" 
                                    name="check[]" value="{{ $item->claimItems[0]->id }}">
                                <label class="custom-control-label" for="pro-check-{{ $i }}"></label>
                            </div>
                        </td>
                        <th scope="row">{{ $i + 1 }}</th>
                        <td>{{ $item->orderLine->productName }}</td>
                        <td>{{ $item->orderLine->merchantSku }}</td>
                        <td>{{ $item->orderLine->barcode }}</td>
                        <td>{{ $item->orderLine->productSize }}</td>
                        <td>{{ $item->orderLine->price }}₺</td>
                        <td>{{ $item->claimItems[0]->customerClaimItemReason->name }}</td>
                        <td>{{ $item->claimItems[0]->trendyolClaimItemReason->name }}</td>
                        <td>{{ $status[$item->claimItems[0]->claimItemStatus->name] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-center">
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
                <div class="col-md-6 mb-2">
                    <input type="file" class="form-control" name="files[]" multiple>
                </div>
                <div class="col-md-6 mb-2">
                    <select name="claimIssueReasonId" class="form-control">
                        <option value="" selected disabled>- İade Nedeni</option>
                        @foreach ($reasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <input type="text" name="description" class="form-control" placeholder="Açıklama">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-cancel mr-1"></i> Reddet</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>