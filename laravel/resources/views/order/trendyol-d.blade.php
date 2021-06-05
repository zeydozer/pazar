<?php

$status =
[
    'Awaiting' => 'Bekliyor',
    'Created' => 'Hazır',
    'Picking' => 'Başladı',
    'Invoiced' => 'Fatura',
    'Shipped' => 'Taşıma',
    'Cancelled' => 'İptal',
    'UnPacked' => 'Bölündü',
    'Delivered' => 'Teslim',
    'UnDelivered' => 'Teslim Edilemedi',
    'UnDeliveredAndReturned' => 'Geri Dönüyor',
    'ReadyToShip' => 'Hazır'
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
    <li class="nav-item">
        <a href="#order-addresses" data-toggle="tab" aria-expanded="false"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-map"></i></span>
            <span class="d-none d-sm-block">Adres</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#histories" data-toggle="tab" aria-expanded="false"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-history"></i></span>
            <span class="d-none d-sm-block">Geçmiş</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#process" data-toggle="tab" aria-expanded="false"
            class="nav-link">
            <span class="d-block d-sm-none"><i class="uil-cog"></i></span>
            <span class="d-none d-sm-block">İşlem</span>
        </a>
    </li>
</ul>
<div class="tab-content p-3 text-muted" data-id="{{ $order->id }}" number="{{ $order->orderNumber }}"
    customer-id="{{ $order->customerId }}">
    <div class="tab-pane" id="order-info">
        <span><b>Tarih:</b><b>{{ date('d.m.Y H:i:s', $order->orderDate / 1000) }}</b></span>
        <span><b>No:</b><b>{{ $order->orderNumber }}</b></span>
        <span><b>İsim:</b><b>{{ $order->customerFirstName }}</b></span>
        <span><b>Soyisim:</b><b>{{ $order->customerLastName }}</b></span>
        <span>
            <b>E-Posta:</b> 
            <a href="mailto: {{ $order->customerEmail }}">
                {{ $order->customerEmail }}
            </a>
        </span>
        <span><b>Tc Kimlik No:</b><b>{{ $order->tcIdentityNumber }}</b></span>
        <span><b>Kargo:</b><b>{{ $order->cargoProviderName }}</b></span>
        @if (isset($order->cargoSenderNumber) && $order->cargoSenderNumber)
        <span><b>Kargo No:</b><b>{{ $order->cargoSenderNumber }}</b></span>
        <span>
            <b>Kargo Takip:</b>
            <a href="{{ $order->cargoTrackingLink }}" target="_blank">
                Tıklayın
            </a>
        </span>
        @endif
        <span><b>Brüt:</b><b>{{ $order->grossAmount }}₺</b></span>
        <span><b>İndirim:</b><b>{{ $order->totalDiscount }}₺</b></span>
        <span><b>Tutar:</b><b>{{ $order->totalPrice }}₺</b></span>
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
                        <th colspan="2">Adet</th>
                        <th>Fiyat</th>
                        <th>İndirim</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>

                    <?php $deci = 0 ?>

                    @foreach ($order->lines as $i => $line)
                    
                    <?php
                    
                    $temp = \App\Http\Controllers\Controller::trendyol('GET', 'products', 
                    [
                        'barcode' => $line->barcode,
                    ]);

                    if (count($temp->content) > 0)
                    
                        $deci += $temp->content[0]->dimensionalWeight;
                    
                    ?>
                    
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="pro-check-{{ $i }}" 
                                    name="check[]" value="{{ $line->id }}">
                                <label class="custom-control-label" for="pro-check-{{ $i }}"></label>
                            </div>
                        </td>
                        <th scope="row">{{ $i + 1 }}</th>
                        <td>{{ $line->productName }}</td>
                        <td>{{ $line->merchantSku }}</td>
                        <td>{{ $line->barcode }}</td>
                        <td>
                            <input type="number" value="{{ $line->quantity }}" 
                                class="form-control" name="quantity[{{ $line->id }}]">
                        </td>
                        <td>{{ $line->productSize }}</td>
                        <td>{{ $line->price }}₺</td>
                        <td>{{ $line->discount }}₺</td>
                        <td>{{ $line->amount }}₺</td>
                        <td>
                            {{ isset($status[$line->orderLineItemStatusName]) ? 
                                    
                                    $status[$line->orderLineItemStatusName] : 
                                    
                                    $line->orderLineItemStatusName }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <div class="row w-50 m-auto">
                <div class="col-md-6">
                    <a href="cancel" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-cancel mr-1"></i> İptal Et</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="divide" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-bag-slash mr-1"></i> Böl</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </a>
                </div>
            </div>
            <form class="border-top row">
                <div class="col-md-8">
                    <select name="shipmentCompanyId" class="form-control">
                        <option value="" selected disabled>- Kargo</option>
                        @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}">{{ $cargo->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <a href="claim" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-backward mr-1"></i> İade Et</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="tab-pane" id="order-addresses">
        <div class="row">
            <div class="col-md-6">
                <h6>Kargo</h6>
                <hr>
                <span><b>İsim:</b><b>{{ $order->shipmentAddress->firstName }}</b></span>
                <span><b>Soyisim:</b><b>{{ $order->shipmentAddress->lastName }}</b></span>
                <span><b>Adres:</b><b>{{ $order->shipmentAddress->address1 }}</b></span>
                @if (isset($order->shipmentAddress->address2) && $order->shipmentAddress->address2)
                <span><b>Adres Devam:</b><b>{{ $order->shipmentAddress->address2 }}</b></span>
                @endif
                @if (isset($order->shipmentAddress->neighborhood) && $order->shipmentAddress->neighborhood)
                <span><b>Mahalle:</b><b>{{ $order->shipmentAddress->neighborhood }}</b></span>
                @endif
                <span><b>İlçe:</b><b>{{ $order->shipmentAddress->district }}</b></span>
                <span><b>İl:</b><b>{{ $order->shipmentAddress->city }}</b></span>
                @if (isset($order->shipmentAddress->postalCode) && $order->shipmentAddress->postalCode)
                <span><b>Posta Kodu:</b><b>{{ $order->shipmentAddress->postalCode }}</b></span>
                @endif
                <span><b>Tam Adres:</b><b>{{ $order->shipmentAddress->fullAddress }}</b></span>
                <span><b>Tam İsim:</b><b>{{ $order->shipmentAddress->fullName }}</b></span>
            </div>
            <div class="col-md-6">
                <h6>Fatura</h6>
                <hr>
                <span><b>İsim:</b><b>{{ $order->invoiceAddress->firstName }}</b></span>
                <span><b>Soyisim:</b><b>{{ $order->invoiceAddress->lastName }}</b></span>
                @if (isset($order->invoiceAddress->company) && $order->invoiceAddress->company)
                <span><b>Firma:</b><b>{{ $order->invoiceAddress->company }}</b></span>
                @endif
                <span><b>Adres:</b><b>{{ $order->invoiceAddress->address1 }}</b></span>
                @if (isset($order->invoiceAddress->address2) && $order->invoiceAddress->address2)
                <span><b>Adres Devam:</b><b>{{ $order->invoiceAddress->address2 }}</b></span>
                @endif
                @if (isset($order->shipmentAddress->neighborhood) && $order->shipmentAddress->neighborhood)
                <span><b>Mahalle:</b><b>{{ $order->invoiceAddress->neighborhood }}</b></span>
                @endif
                <span><b>İlçe:</b><b>{{ $order->invoiceAddress->district }}</b></span>
                <span><b>İl:</b><b>{{ $order->invoiceAddress->city }}</b></span>
                <span><b>Tam Adres:</b><b>{{ $order->invoiceAddress->fullAddress }}</b></span>
                <span><b>Tam İsim:</b><b>{{ $order->invoiceAddress->fullName }}</b></span>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="histories">
        
        <?php // usort($order->packageHistories, fn($a, $b) => strcmp($a->createdDate, $b->createdDate)) ?>

        @foreach ($order->packageHistories as $history)
        <div>
            <span><b>Tarih:</b><b>{{ date('d.m.Y H:i:s', $history->createdDate / 1000) }}</b></span>
            <span>
                <b>Durum:</b>
                <b>{{ isset($status[$history->status]) ? $status[$history->status] : $history->status }}</b>
            </span>
        </div>
        @endforeach
    </div>
    <div class="tab-pane" id="process">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label>Kargo Numarası</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="cargo">
                        <div class="input-group-append">
                            <button class="input-group-text">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="sr-only">Yükleniyor...</span>
                                </div>
                                <i class="uil uil-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>İşleme Al</label>
                    <div class="input-group" aria-describedby="pick-help">
                        <input type="text" class="form-control" name="pick" placeholder="Fatura No">
                        <div class="input-group-append">
                            <button class="input-group-text">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="sr-only">Yükleniyor...</span>
                                </div>
                                <i class="uil uil-refresh"></i>
                            </button>
                        </div>
                    </div>
                    <small id="pick-help" class="form-text text-muted">Fatura numarası zorunlu değil.</small>
                </div>
                <div class="form-group">
                    <label>Fatura Link</label>
                    <div class="input-group">
                        <input type="url" class="form-control" name="invoice">
                        <div class="input-group-append">
                            <button class="input-group-text">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="sr-only">Yükleniyor...</span>
                                </div>
                                <i class="uil uil-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="cargo">
                    <label>Kargo Detayı</label>
                    <div class="input-group">
                        <input class="form-control" type="number" placeholder="Kutu" name="box">
                        <input class="form-control" type="number" placeholder="Desi" name="deci" 
                            step="0.01" @if ($deci) value="{{ $deci }}" @endif>
                        <div class="input-group-append">
                            <button class="input-group-text">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="sr-only">Yükleniyor...</span>
                                </div>
                                <i class="uil uil-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>