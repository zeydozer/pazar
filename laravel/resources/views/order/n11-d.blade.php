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
</ul>
<div class="tab-content p-3 text-muted">
    <div class="tab-pane" id="order-info">
        <span><b>Tarih:</b><b>{{ str_replace('/', '.', $order->createDate) }}</b></span>
        <span><b>No:</b><b>{{ $order->orderNumber }}</b></span>
        <span><b>İsim Soyisim:</b><b>{{ $order->buyer->fullName }}</b></span>
        <span>
            <b>E-Posta:</b> 
            <a href="mailto: {{ $order->buyer->email }}">
                {{ $order->buyer->email }}
            </a>
        </span>
        @if (isset($order->citizenshipId) && $order->citizenshipId)
        <span><b>Tc Kimlik No:</b><b>{{ $order->citizenshipId }}</b></span>
        @endif
        
        <?php $invoice = ['Bireysel', 'Kurumsal'] ?>
        
        <span><b>Fatura:</b><b>{{ $invoice[$order->invoiceType - 1] }}</b></span>

        <?php 
        
        $payment = ['Kredi Kartı', 'BKMEXPRESS', 'AKBANKDIREKT', 'PAYPAL', 'MallPoint', 
            'GARANTIPAY', 'GarantiLoan', 'MasterPass', 'ISBANKPAY', 'PAYCELL', 'COMPAY', 
            'YKBPAY', 'Diğer']
                        
        ?>

        <span><b>Ödeme:</b><b>{{ $payment[$order->paymentType - 1] }}</b></span>

        <?php $status = ['İşlem Bekliyor', 'İşlemde', 'İptal Edilmiş', 'Geçersiz', 'Tamamlandı'] ?>

        <span><b>Durum:</b><b>{{ $status[$order->status - 1] }}</b></span>
        <span><b>Brüt:</b><b>{{ $order->billingTemplate->originalPrice }}₺</b></span>
        <span><b>İndirim:</b><b>{{ $order->billingTemplate->totalSellerDiscount }}₺</b></span>
        <span><b>Tutar:</b><b>{{ $order->billingTemplate->dueAmount }}₺</b></span>
    </div>
    <div class="tab-pane show active" id="order-products">
        <div class="table-responsive mb-3">
            <table class="table m-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>İsim</th>
                        <th>Özellik</th>
                        <th>Stok Kodu</th>
                        <th>Adet</th>
                        <th>Fiyat</th>
                        <th>İndirim</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Onay</th>
                        <th>Kargo</th>
                        <th>Durum</th>
                        <th>Firma</th>
                        <th>Kargo No</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    
                    if (!is_array($order->itemList->item))

                        $order->itemList->item = [$order->itemList->item];
                    
                    ?>

                    @foreach ($order->itemList->item as $i => $item)
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="pro-check-{{ $i }}" 
                                    name="check[]" value="{{ $item->id }}">
                                <label class="custom-control-label" for="pro-check-{{ $i }}"></label>
                            </div>
                        </td>
                        <th scope="row">{{ $i + 1 }}</th>
                        <td>{{ $item->productName }}</td>
                        <td>
                            @if (isset($item->attributes->attribute))
                            <?php
                    
                            if (!is_array($item->attributes->attribute))

                                $item->attributes->attribute = [$item->attributes->attribute];
                            
                            ?>

                            @foreach ($item->attributes->attribute as $attribute)
                            <span class="d-block">
                                <b>{{ $attribute->name }}:</b> {{ $attribute->value }}
                            </span>
                            @endforeach
                            @else
                            <i class="uil uil-minus"></i>
                            @endif
                        </td>
                        <td>{{ $item->productSellerCode }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->price }}₺</td>
                        <td>{{ $item->sellerDiscount }}₺</td>
                        <td>{{ $item->dueAmount }}₺</td>
                        
                        <?php 
                        
                        $status = ['İşlem Bekliyor', 'Ödendi', 'Geçersiz', 'İptal Edilmiş', 'Kabul Edilmiş', 
                            'Kargoda', 'Teslim Edilmiş', 'Reddedilmiş', 'İade Edildi', 'Tamamlandı', 
                            'İade İptal Değişim Talep Edildi', 'İade İptal Değişim Tamamlandı', 'Kargoda İade', 
                            'Kargo Yapılması Gecikmiş', 'Kabul Edilmiş Ama Zamanında Kargoya Verilmemiş', 
                            'Teslim Edilmiş İade', 'Tamamlandıktan Sonra İade']
                        
                        ?>

                        <td>{{ $status[$item->status - 1] }}</td>
                        <td>{{ str_replace('/', '.', $item->approvedDate) }}</td>
                        <td>{{ str_replace('/', '.', $item->shippingDate) }}</td>

                        <?php 
                        
                        $status = ['N11 Öder', 'Alıcı Öder', 'Mağaza Öder', 'Şartlı Kargo (Alıcı Öder)', 
                            'Şartlı Kargo Ücretsiz (Satıcı Öder)'] 
                        
                        ?>

                        <td>{{ $status[$item->deliveryFeeType - 1] }}</td>
                        <td>{{ $item->shipmentInfo->shipmentCompany->name }}</td>
                        <td>
                            @if (isset($item->shipmentInfo->trackingNumber))
                            {{ $item->shipmentInfo->trackingNumber }}
                            @else
                            <i class="uil uil-minus"></i>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <form class="row">
                <div class="col-md-8">
                    <input type="number" name="pocket" class="form-control"
                        placeholder="Paket Sayısı" min="1">
                </div>
                <div class="col-md-4">
                    <a href="accept" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-check mr-1"></i> Onayla</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </a>
                </div>
            </form>
            <form class="border-top row">
                <div class="col-md-4">
                    <input type="text" name="reason" class="form-control"
                        placeholder="Açıklama">
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-control">
                        <option value="OUT_OF_STOCK">Stokta Yok</option>
                        <option value="OTHER">Diğer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <a href="reject" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-cancel mr-1"></i> Reddet</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </a>
                </div>
            </form>
            @if (isset($cargos))
            <form class="border-top row">
                <div class="col-md-4">
                    <select name="cargo" class="form-control">
                        <option value="" selected disabled>- Kargo</option>
                        @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}">{{ $cargo->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="number" class="form-control"
                        placeholder="Takip No">
                </div>
                <div class="col-md-4">
                    <a href="cargo" class="btn btn-secondary btn-block">
                        <text><i class="uil uil-truck-loading mr-1"></i> Kargo</text>
                        <div class="spinner-border text-white" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                    </a>
                </div>
            </form>
            @endif
        </div>
    </div>
    <div class="tab-pane" id="order-addresses">
        <div class="row">
            <div class="col-md-6">
                <h6>Kargo</h6>
                <hr>
                <span><b>İsim Soyisim:</b><b>{{ $order->shippingAddress->fullName }}</b></span>
                <span><b>Adres:</b><b>{{ $order->shippingAddress->address }}</b></span>
                @if (isset($order->shippingAddress->postalCode))
                <span><b>Posta Kodu:</b><b>{{ $order->shippingAddress->postalCode }}</b></span>
                @endif
                @if (isset($order->shippingAddress->neighborhood))
                <span><b>Mahalle:</b><b>{{ $order->shippingAddress->neighborhood }}</b></span>
                @endif
                <span><b>İlçe:</b><b>{{ $order->shippingAddress->district }}</b></span>
                <span><b>İl:</b><b>{{ $order->shippingAddress->city }}</b></span>
                @if (isset($order->shippingAddress->gsm))
                <span>
                    <b>Gsm:</b>
                    <b>
                        <a href="tel: +90{{ $order->shippingAddress->gsm }}">
                            {{ $order->shippingAddress->gsm }}
                        </a>
                    </b>
                </span>
                @endif
                @if (isset($order->shippingAddress->tcId))
                <span><b>Tc Kimlik No:</b><b>{{ $order->shippingAddress->tcId }}</b></span>
                @endif
            </div>
            <div class="col-md-6">
                <h6>Fatura</h6>
                <hr>
                <span><b>İsim Soyisim:</b><b>{{ $order->billingAddress->fullName }}</b></span>
                <span><b>Adres:</b><b>{{ $order->billingAddress->address }}</b></span>
                @if (isset($order->billingAddress->postalCode))
                <span><b>Posta Kodu:</b><b>{{ $order->billingAddress->postalCode }}</b></span>
                @endif
                @if (isset($order->billingAddress->neighborhood))
                <span><b>Mahalle:</b><b>{{ $order->billingAddress->neighborhood }}</b></span>
                @endif
                <span><b>İlçe:</b><b>{{ $order->billingAddress->district }}</b></span>
                <span><b>İl:</b><b>{{ $order->billingAddress->city }}</b></span>
                @if (isset($order->billingAddress->gsm))
                <span>
                    <b>Gsm:</b>
                    <b>
                        <a href="tel: +90{{ $order->billingAddress->gsm }}">
                            {{ $order->billingAddress->gsm }}
                        </a>
                    </b>
                </span>
                @endif
                @if (isset($order->billingAddress->tcId))
                <span><b>Tc Kimlik No:</b><b>{{ $order->billingAddress->tcId }}</b></span>
                @endif
            </div>
        </div>
    </div>
</div>