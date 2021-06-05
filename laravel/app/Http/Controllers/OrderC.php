<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use \App\Models\Invoice;
use \App\Models\Match;
use \App\Models\Product;

class OrderC extends Controller
{
    public static function auto_stock()
    {
        $products = Product::where('match', '!=', null)
            ->where('del', 0)
            ->orderBy('code')
            ->get();

        foreach ($products as $product) :
            
            repeat:

            $product_p = self::parasut('product', 'show', null, $product->match);

            if (!isset($product_p['errors']))
            {
                $message = [];

                $updated_at = date('Y-m-d H:i:s', strtotime($product_p['data']['attributes']['updated_at'] .' -3 hour'));

                $stock_p = intval($product_p['data']['attributes']['stock_count']);

                if (/* $updated_at < date('Y-m-d H:i:s', strtotime('-60 minute')) || */ $product->stock == $stock_p)
                {
                    $message[] = $product->code ." / ". $updated_at ." / ". date('Y-m-d H:i:s', strtotime('-60 minute')) ."\n";

                    goto next_loop;
                }

                \DB::connection('commerce')->table('urun')->where('id', $product->id)->update(['stok' => $stock_p]);

                $product->update(['stock' => $stock_p, 'prepare' => $stock_p > 0 ? 0 : 3]);

                /* $merchant = \App\Http\Controllers\ProductC::merchant_put($product);

                if (!$merchant)
                
                    $message[] = $product->code ." merchant center a aktarilamadi\n"; */

                $trendyol = Match::from('match AS m')
                    ->join('product AS p', function($j)
                    {
                        $j->on('p.id', '=', 'm.product_id')
                            ->where('p.del', 0);
                    })
                    ->where('m.type', 'trendyol')
                    ->where('m.product_id', $product->id)
                    ->select('m.code', 'p.price', 'p.discount', 'p.stop')
                    ->get();

                if (count($trendyol) > 0) :
 
                    $query = [];

                    foreach ($trendyol as $i => $temp)
                    {
                        $query[$i] =
                        [
                            'barcode' => $temp->code,
                            'quantity' => $temp->stop ? 0 : $stock_p,
                        ];
                    }

                    $result = self::trendyol('POST', 'products/price-and-inventory', 
                    [
                        'items' => $query
                    ]);                    

                    if (!isset($result->batchRequestId))
                    {
                        $message[] = $product->code ." trendyol a stok aktarilamadi\n";

                        // return [false, 'Trendyol stok güncelleme hatası.'];
                    }

                endif;

                $n11 = Match::from('match AS m')
                    ->join('product AS p', function($j)
                    {
                        $j->on('p.id', '=', 'm.product_id')
                            ->where('p.del', 0);
                    })
                    ->where('m.type', 'n11')
                    ->where('m.product_id', $product->id)
                    ->select('m.code', 'p.price', 'p.discount', 'p.stop_n')
                    ->get();

                if (count($n11) > 0) :
 
                    $control = [];

                    foreach ($n11 as $i => $temp)
                    {
                        $result = self::n11('ProductStockService', 'UpdateStockByStockSellerCode',
                        [
                            'stockItems' =>
                            [
                                'stockItem' =>
                                [
                                    'sellerStockCode' => $temp->code,
                                    'quantity' => $temp->stop_n ? 0 : $stock_p,
                                    'version' => null      
                                ]
                            ]
                        ]);

                        if (isset($result->result->status))
                        {
                            if ($result->result->status != 'success')

                                $control[] = $temp->code;                                    
                        }

                        else $control[] = $temp->code;
                    }

                    if (count($control) == count($n11))
                    {
                        $message[] = $product->code ." n11 e stok aktarilamadi\n";

                        // return [false, 'N11 stok güncelleme hatası.'];
                    }

                endif;

                $ciceksepeti = Match::from('match AS m')
                    ->join('product AS p', function($j)
                    {
                        $j->on('p.id', '=', 'm.product_id')
                            ->where('p.del', 0);
                    })
                    ->where('m.type', 'ciceksepeti')
                    ->where('m.product_id', $product->id)
                    ->select('m.code', 'p.price', 'p.discount', 'p.stop_c')
                    ->get();

                if (count($ciceksepeti) > 0) :
 
                    $query = [];

                    $column_t = ['price_c' => 'listPrice', 'discount_c' => 'salesPrice'];

                    foreach ($ciceksepeti as $i => $data)
                    {
                        $query[$i] = 
                        [
                            'stockCode' => $data->code,
                            'stockQuantity' => $temp->stop_c ? 0 : $stock_p
                        ];

                        foreach ($column_t as $name => $name_c)

                            $query[$i][$name_c] = $incoming->$name;
                    }

                    $result = self::c_s('PUT', 'Products/price-and-stock',
                    [
                        'items' => $query
                    ]);

                    if (!isset($result->batchId))
                    {
                        $message[] = $product->code ." ciceksepeti ne stok aktarilamadi\n";

                        // return [false, 'Ciceksepeti stok güncelleme hatası.'];
                    }

                endif;

                next_loop:

                /* if (!count($message))
                {
                    $product->update(['updated' => date('Y-m-d H:i:s')]);

                    echo $product->code ." ". $product->stock ."\n";
                }

                else echo implode('', $message); */
            }

            else
            {
                // echo $product->code ." parasut ten urun cekilemedi\n";

                goto repeat;

                // return [false, 'Stok güncelleme hatası.'];
            }
        
        endforeach;
    }

    // trendyol

    public function list_trendyol(Request $r)
    {
        $show = $data['show'] = 200;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        {
            $date = explode(' | ', $r->get('date'));

            list($day, $month, $year) = explode('.', $date[0]);

            $date[0] = date('Y-m-d H:i:s', strtotime($year .'-'. $month .'-'. $day));

            list($day, $month, $year) = explode('.', $date[1]);

            $date[1] = date('Y-m-d H:i:s', strtotime($year .'-'. $month .'-'. $day .' 23:59:59'));
        }

        else
        {
            $date =
            [
                date('Y-m-d H:i:s', strtotime('-2 week', strtotime(date('Y-m-d')))),
                date('Y-m-d H:i:s', strtotime('+1 days', strtotime(date('Y-m-d')))),
            ];
        }

        $data['datas'] = Controller::trendyol('GET', 'orders',
        [
            'startDate' => strtotime($date[0]) * 1000,
            'endDate' => strtotime($date[1]) * 1000,
            'size' => $show, 
            'page' => $page - 1,
            'orderByField' => 'CreatedDate',
            'orderByDirection' => 'DESC'
        ]);

        return view('order.trendyol', $data);
    }

    public function return_trendyol(Request $r)
    {
        $show = $data['show'] = 200;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        $data['datas'] = Controller::trendyol('GET', 'claims',
        [
            'size' => $show, 
            'page' => $page - 1,
        ]);

        return view('claim.trendyol', $data);
    }

    public function bank_trendyol(Request $r)
    {
        $show = $data['show'] = 200;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        {
            $date = explode(' | ', $r->get('date'));

            list($day, $month, $year) = explode('.', $date[0]);

            $date[0] = date('Y-m-d H:i:s', strtotime($year .'-'. $month .'-'. $day));

            list($day, $month, $year) = explode('.', $date[1]);

            $date[1] = date('Y-m-d H:i:s', strtotime($year .'-'. $month .'-'. $day .' 23:59:59'));
        }

        else
        {
            $date =
            [
                date('Y-m-d H:i:s', strtotime('-10 day', strtotime(date('Y-m-d')))),
                date('Y-m-d H:i:s', strtotime('+1 day', strtotime(date('Y-m-d')))),
            ];
        }

        $data['datas'] = Controller::trendyol('GET', 'settlements',
        [
            'dateType' => 'Process',
            'startDate' => strtotime($date[0]) * 1000,
            'endDate' => strtotime($date[1]) * 1000,
            'size' => $show, 
            'page' => $page - 1,
        ]);

        return view('bill.trendyol', $data);
    }

    public static function invoice_trendyol(Invoice $invoice)
    {
        $order = self::trendyol('GET', 'orders',
        [
            'orderNumber' => $invoice->order_id
        ]);

        if (!isset($order->content[0]))
        {
            echo $invoice->order_id ." trendyol dan siparis cekilemedi\n";

            return [false, 'Trendyol sorgu hatası.'];
        }

        else $order = $order->content[0];

        $match = [];

        foreach ($order->lines as $line) :

            // $line->barcode = '887961428506';

            $temp = Match::join('product', 'product.id', '=', 'match.product_id')
                ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
                ->where('match.type', 'trendyol')
                ->where('match.code', $line->barcode)
                ->where('product.del', 0)
                ->select('product.*', 'brand.name AS brand')
                ->first();

            if (!$temp)
            {
                echo $line->barcode ." urun eslestirilmemis\n";

                return [false, $line->barcode .' barkodlu ürün eşleştirilmemiş.'];
            }

            $match[$line->barcode] = $temp;

        endforeach;

        if (!$order->taxNumber) :

            $order->taxNumber = $order->tcIdentityNumber;

            if ($order->taxNumber == '99999999999')

                $order->taxNumber = '00000000000';

        endif;

        if (!$invoice->account_id) :

            $data = 
        
            '{
                "data": {
                    "id": "T'. $order->invoiceAddress->id .'",
                    "type": "contacts",
                    "attributes": {
                        "email": "'. $order->customerEmail .'",
                        "name": "'. $order->invoiceAddress->fullName .'",
                        "contact_type": "person",
                        "tax_number": "'. $order->taxNumber .'",
                        "district": "'. $order->invoiceAddress->district .'",
                        "city": "'. $order->invoiceAddress->city .'",
                        "address": "'. stripslashes($order->invoiceAddress->fullAddress) .'",
                        "is_abroad": false,
                        "archived": false,
                        "account_type": "customer"
                    },
                    "relationships": {
                        "category": {
                            "data": {
                                "id": "5142078",
                                "type": "item_categories"
                            }
                        }
                    }
                }
            }';

            $result = self::parasut('account', 'create', $data);
            
            if (!isset($result['data']['id']))
            {
                echo $invoice->order_id ." musteri parasut e aktarilamadi\n";

                return [false, 'Müşteri kaydedilemedi.'];
            }

            else
            {
                $invoice->account_id = $result['data']['id'];

                $invoice->save();
            }

        endif;

        if (!$invoice->sales_id) :

            $temp = date('Y-m-d');

            $due_date = date('l', strtotime($temp));

            if ($due_date != 'Thursday')
            {
                while ($due_date != 'Thursday')
                {
                    $due_date = date('l', strtotime($temp .' +1 days'));

                    $temp = date('Y-m-d', strtotime($temp .' +1 days'));
                }
            }

            $due_date = date('Y-m-d', strtotime($temp .' +14 days'));

            $data =

            '{
                "data": {
                    "type": "sales_invoices",
                    "attributes": {
                        "item_type": "invoice",
                        "description": "Trendyol #'. $order->orderNumber .'",
                        "issue_date": "'. date('Y-m-d') .'",
                        "due_date": "'. $due_date .'",
                        "currency": "TRL",
                        "billing_address": "'. stripslashes($order->invoiceAddress->fullAddress) .'",
                        "tax_office": null,
                        "tax_number": "'. $order->taxNumber .'",
                        "city": "'. $order->invoiceAddress->city .'",
                        "district": "'. $order->invoiceAddress->district .'",
                        "order_no": "'. $order->orderNumber .'",
                        "order_date": "'. date('Y-m-d') .'",
                        "shipment_addres": "'. stripslashes($order->shipmentAddress->fullAddress) .'",
                        "shipment_included": true
                    },
                    "relationships": {
                        "details": {
                            "data": [';

                            $temp = [];

                            foreach ($order->lines as $line) :

                                $desc = [$match[$line->barcode]->code, $match[$line->barcode]->brand, $line->barcode];

                                $desc = implode(' ', array_filter($desc));

                                if ($line->vatBaseAmount > 0)

                                    $line->price = $line->price / (1 + ($line->vatBaseAmount / 100));
                                
                                $temp[] =
                                
                                '{
                                    "type": "sales_invoice_details",
                                    "attributes": {
                                        "quantity": '. $line->quantity .',
                                        "unit_price": '. $line->price .',
                                        "vat_rate": '. $line->vatBaseAmount .',
                                        "description": "'. $desc .'"
                                    },
                                    "relationships": {
                                        "product": {
                                            "data": {
                                                "id": "'. $match[$line->barcode]->match .'",
                                                "type": "products"
                                            }
                                        }
                                    }
                                }';
                                
                            endforeach;

                            $data .= implode(',', $temp) .
                        
                            ']
                        },
                        "contact": {
                            "data": {
                                "id": "'. $invoice->account_id .'",
                                "type": "contacts"
                            }
                        },
                        "category": {
                            "data": {
                                "id": "5142056",
                                "type": "item_categories"
                            }
                        },
                        "tags": {
                            "data": [
                                {
                                    "id": "341748",
                                    "type": "tags"
                                },
                                {
                                    "id": "339006",
                                    "type": "tags"
                                }
                            ]
                        }
                    }
                }
            }';

            $result = self::parasut('invoice', 'create', $data);

            if (!isset($result['data']['id']))
            {
                echo $invoice->order_id ." fatura parasut e aktarilamadi\n";
            
                return [false, 'Fatura kaydedilemedi.'];
            }
                
            else
            {
                $invoice->sales_id = $result['data']['id'];

                $invoice->save();
            }

        endif;

        return [true, $order];
    }

    // live

    public function live(Request $r)
    {
        if ($r->get('type') == 'trendyol')
        {
            if ($r->get('task') == 'detail')
            {
                $result = Controller::trendyol('GET', 'orders',
                [
                    'orderNumber' => $r->get('number'),
                    // 'shipmentPackageIds' => $r->get('id'),
                ]);

                if (isset($result->content))
                {
                    if (count($result->content) > 0)
                    {
                        $data = ['order' => $result->content[0]];

                        $data['cargos'] = Controller::trendyol('GET', 'shipment-providers', null, false);

                        return view('order.trendyol-d', $data)->render();
                    }

                    else return false;
                }

                else return false;
            }

            else if ($r->get('task') == 'cargo')
            {
                $result = Controller::trendyol('PUT', $r->get('id'). '/update-tracking-number',
                [
                    'trackingNumber' => $r->get('value')
                ]);

                /* if (isset($result->errors))

                    echo $result->errors[0]->message; */

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'pick')
            {
                $result = Controller::trendyol('GET', 'orders',
                [
                    'orderNumber' => $r->get('number'),
                ]);

                if (isset($result->content))
                {
                    if (count($result->content) > 0)

                        $order = $result->content[0];

                    else return false;
                }

                else return false;

                $lines = [];

                foreach ($order->lines as $line)

                    $lines[] = ['lineId' => (int) $line->id, 'quantity' => (int) $line->quantity];

                $result = Controller::trendyol('PUT', 'shipment-packages/'. $r->get('id'),
                [
                    'lines' => $lines,
                    'params' => new \stdClass(),
                    'status' => 'Picking',
                ]);

                if (isset($result->errors))

                    return false;

                if ($r->has('value'))
                {
                    $result = Controller::trendyol('PUT', 'shipment-packages/'. $r->get('id'),
                    [
                        'lines' => $lines,
                        'params' => ['invoiceNumber' => $r->get('value')],
                        'status' => 'Invoiced',
                    ]);

                    return isset($result->errors) ? false : true;
                }

                else return true;
            }

            else if ($r->get('task') == 'invoice')
            {
                $result = Controller::trendyol('POST', 'supplier-invoice-links',
                [
                    'invoiceLink' => $r->get('value'),
                    'shipmentPackageId' => (int) $r->get('id')
                ]);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'box-deci')
            {
                $result = Controller::trendyol('PUT', 'shipment-packages/'. $r->get('id') .'/box-info',
                [
                    'boxQuantity' => (int) $r->get('box'),
                    'deci' => (float) $r->get('deci')
                ]);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'cancel')
            {
                $lines = [];

                foreach ($r->get('line') as $id => $quantity)

                    $lines[] = ['lineId' => (int) $id, 'quantity' => (int) $quantity];

                $result = Controller::trendyol('PUT', 'shipment-packages/'. $r->get('id'),
                [
                    'lines' => $lines, 
                    'params' => new \stdClass(), 
                    'status' => 'UnSupplied'
                ]);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'divide')
            {
                $lines = ['packageDetails' => []];

                foreach ($r->get('line') as $id => $quantity)

                    $lines['packageDetails'][] = ['orderLineId' => (int) $id, 'quantities' => (int) $quantity];

                $result = Controller::trendyol('POST', 'shipment-packages/'. $r->get('id') .'/split-packages',
                [
                    'splitPackages' => [$lines]
                ]);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'detail-r')
            {
                $result = Controller::trendyol('GET', 'claims',
                [
                    'claimIds' => $r->get('id'),
                ]);

                if (isset($result->content))
                {
                    if (count($result->content) > 0)
                    {
                        $data = ['order' => $result->content[0]];

                        $data['reasons'] = Controller::trendyol('GET', 'claim-issue-reasons', null, false);

                        return view('claim.trendyol-d', $data)->render();
                    }

                    else return false;
                }

                else return false;
            }

            else if ($r->get('task') == 'approve')
            {
                $lines = ['claimLineItemIdList' => [], 'params' => new \stdClass()];

                foreach ($r->get('line') as $id)

                    $lines['claimLineItemIdList'][] = $id;

                $result = Controller::trendyol('PUT', 'claims/'. $r->get('id') .'/items/approve', $lines, false);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'issue')
            {
                $lines = '["'. implode('", "', $r->get('line')) .'"]';

                $url = 'claims/'. $r->get('id') .'/issue?claimIssueReasonId='. $r->get('claimIssueReasonId');

                $url .= '&claimItemIdList='. $lines .'&description=';

                return json_encode($r->file('files')[0]->getPathName());

                $result = Controller::trendyol('PUT', $url, ['files' => '@'. $r->file('photo')], false);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'claim')
            {
                $param = 
                [
                    'customerId' => (int) $r->get('customer'),
                    'excludeListing' => true,
                    'forcePackageCreation' => true,
                    'orderNumber' => $r->get('number'),
                    'shipmentCompanyId' => (int) $r->get('cargo'),
                    'claimItems' => [],
                ];

                foreach ($r->get('line') as $id => $quantity)
                {
                    $param['claimItems'][] = 
                    [
                        'barcode' => $r->get('barcode')[$id],
                        'customerNote' => 'İade kodu olmadan gelen iade.', 
                        'quantity' => (int) $quantity,
                        'reasonId' => 401
                    ];
                }

                $result = Controller::trendyol('POST', 'claims/create', $param);

                return isset($result->errors) ? false : true;
            }

            else if ($r->get('task') == 'invoice-s')
            {
                $invoice = \App\Models\Invoice::where('type', 'trendyol')
                    ->where('order_id', $r->get('id'))
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

                return $temp;
            }
        }

        else if ($r->get('type') == 'n11')
        {
            if ($r->get('task') == 'detail')
            {
                $result = Controller::n11('OrderService', 'OrderDetail',
                [
                    'orderRequest' => ['id' => $r->get('id')]
                ]); 

                if (isset($result->orderDetail))
                {
                    $data['order'] = $result->orderDetail;

                    $results = Controller::n11('ShipmentCompanyService', 'GetShipmentCompanies');

                    if (isset($results->shipmentCompanies->shipmentCompany))
                    
                        $data['cargos'] = $results->shipmentCompanies->shipmentCompany;

                    return view('order.n11-d', $data)->render();
                }

                else return false;
            }

            else if ($r->get('task') == 'accept')
            {
                $param = ['orderItemList' => ['orderItem' => []]];

                foreach ($r->get('ids') as $id)
                
                    $param['orderItemList']['orderItem'][] = ['id' => $id];

                $param['numberOfPackages'] = $r->get('pocket');

                $result = Controller::n11('OrderService', 'OrderItemAccept', $param);

                return isset($result->result->orderItemList->orderItem) ? true : false;
            }

            else if ($r->get('task') == 'reject')
            {
                $param = ['orderItemList' => ['orderItem' => []]];

                foreach ($r->get('ids') as $id)
                
                    $param['orderItemList']['orderItem'][] = ['id' => $id];

                $param['rejectReason'] = $r->get('reason');

                $param['rejectReasonType'] = $r->get('type');

                $result = Controller::n11('OrderService', 'OrderItemReject', $param);

                return isset($result->result->orderItemList->orderItem) ? true : false;
            }

            else if ($r->get('task') == 'cargo')
            {
                $param = ['orderItemList' => ['orderItem' => []]];

                foreach ($r->get('ids') as $id)
                {
                    $param['orderItemList']['orderItem'][] = 
                    [
                        'id' => $id,
                        'shipmentInfo' => 
                        [
                            'shipmentCompany' => 
                            [
                                'id' => $r->get('cargo')
                            ]
                        ],
                        'trackingNumber' => $r->get('number'),
                        'shipmentMethod' => 1
                    ];
                }

                $result = Controller::n11('OrderService', 'MakeOrderItemShipment', $param);

                return isset($result->result->orderItemList->orderItem) ? true : false;
            }

            else if ($r->get('task') == 'approve')
            {
                $result = Controller::n11('ClaimCancelService', 'ClaimCancelApprove',
                [
                    'claimCancelId' => $r->get('id')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'deny')
            {
                $result = Controller::n11('ClaimCancelService', 'ClaimCancelDeny',
                [
                    'claimCancelId' => $r->get('id'),
                    'denyReasonId' => $r->get('reason'),
                    'denyReasonNote' => $r->get('description')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'approve-r')
            {
                $result = Controller::n11('ReturnService', 'ClaimReturnApprove',
                [
                    'claimReturnlId' => $r->get('id')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'deny-r')
            {
                $result = Controller::n11('ReturnService', 'ClaimReturnDeny',
                [
                    'claimReturnlId' => $r->get('id'),
                    'denyReasonId' => $r->get('reason'),
                    'denyReasonNote' => $r->get('description')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'pend-r')
            {
                $result = Controller::n11('ReturnService', 'ClaimReturnPending',
                [
                    'claimReturnlId' => $r->get('id'),
                    'pendingReasonId' => $r->get('reason'),
                    'pendingDayCount' => $r->get('day'),
                    'pendingReasonNote' => $r->get('description')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'approve-c')
            {
                if ($r->get('status') == 'cargo-a')
                {
                    $func = 'ExchangeApproveByTrackingNumber';

                    $param = 
                    [
                        'claimeExchangeId' => $r->get('id'),
                        'shipmentCompanyId' => $r->get('cargo'),
                        'trackingNumber' => $r->get('number')
                    ];
                }

                else if ($r->get('status') == 'cargo-p')
                {
                    $func = 'ExchangeApproveByCargoCampaign';

                    $param = ['claimeExchangeId' => $r->get('id')];
                }

                else if ($r->get('status') == 'self')
                {
                    $func = 'ExchangeApproveByReceiptNumber';

                    $param = 
                    [
                        'claimeExchangeId' => $r->get('id'),
                        'deliveryReceiptNumber' => $r->get('number')
                    ];
                }

                $result = Controller::n11('ClaimExchangeService', $func, $param);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'deny-c')
            {
                $result = Controller::n11('ClaimExchangeService', 'ClaimExchangeDeny',
                [
                    'claimeExchangeId' => $r->get('id'),
                    'denyReasonId' => $r->get('reason'),
                    'denyReasonNote' => $r->get('description')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'pend-c')
            {
                $result = Controller::n11('ClaimExchangeService', 'ClaimExchangePending',
                [
                    'claimExchangelId' => $r->get('id'),
                    'pendingReasonId' => $r->get('reason'),
                    'pendingDayCount' => $r->get('day'),
                    'pendingReasonNote' => $r->get('description')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'confirm-c')
            {
                $result = Controller::n11('ClaimExchangeService', 'ClaimExchangeDenyWithConfirm',
                [
                    'claimExchangelId' => $r->get('id'),
                    'denyReasonId' => $r->get('reason'),
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'invoice-s')
            {
                $invoice = \App\Models\Invoice::where('type', 'n11')
                    ->where('order_id', $r->get('id'))
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

                return $temp;
            }
        }
    }

    public function invoice(Request $r)
    {
        $invoice = Invoice::where('type', $r->get('type'))
            ->where('order_id', $r->get('id'))
            ->first();

        if (!$invoice) :

            $invoice = new Invoice;

            $invoice->type = $r->get('type');

            $invoice->order_id = $r->get('id');

            $invoice->save();

        endif;

        try
        {
            if ($invoice->type == 'trendyol') :

                $result = self::invoice_trendyol($invoice);

                if (!$result[0])

                    return $result;

                else $order = $result[1];
                
                if (!$invoice->e_track) :

                    $cargo = 
                    [
                        'MNG Kargo Marketplace' => ['MNG', 6080712084],
                        'Trendyol Express Marketplace' => ['TRENDYOL LOJİSTİK A.Ş', 8590921777],
                    ];

                    if (!$invoice->status)
                    {
                        $check = self::parasut('invoice', 'checkType', $order->taxNumber);

                        $invoice->status = isset($check['data'][0]['id']) ? 'invoice' : 'archive';

                        $invoice->save();
                    }

                    if ($invoice->status == 'invoice') :

                        $data =

                        '{
                            "data": {
                                "type": "e_invoices",
                                "attributes": {
                                    "note": "Trendyol '. $order->orderNumber .'",
                                    "scenario": "basic",
                                    "to": "'. $check['data'][0]['e_invoice_address'] .'"
                                },
                                "relationships": {
                                    "invoice": {
                                        "data": {
                                            "id": "'. $invoice->sales_id .'",
                                            "type": "sales_invoices"
                                        }
                                    }
                                }
                            }
                        }';

                    else :

                        $data =

                        '{
                            "data": {
                                "type": "e_archives",
                                "attributes": {
                                    "note": "Trendyol '. $order->orderNumber .'",
                                    "internet_sale": {
                                        "url": "trendyol.com",
                                        "payment_type": "ODEMEARACISI",
                                        "payment_platform": "Trendyol",
                                        "payment_date": "'. date('Y-m-d') .'"
                                    },
                                    "shipment": {
                                        "title": "'. $cargo[$order->cargoProviderName][0] .'",
                                        "vkn": "'. $cargo[$order->cargoProviderName][1] .'",
                                        "date": "'. date('Y-m-d') .'"
                                    }
                                },
                                "relationships": {
                                    "sales_invoice": {
                                        "data": {
                                            "id": "'. $invoice->sales_id .'",
                                            "type": "sales_invoices"
                                        }
                                    }
                                }
                            }
                        }';

                    endif;

                    $result = self::parasut('invoice', 'create_e_'. $invoice->status, $data);

                    if (!isset($result['data']['id']) || 
                        (isset($result['data']['attributes']['status']) && 
                            $result['data']['attributes']['status'] == 'error'))
                        
                        return [false, 'Fatura resmileştirilemedi.'];

                    else
                    {
                        $invoice->e_track = $result['data']['id'];
        
                        $invoice->save();
                    }

                endif;

                if (!$invoice->e_id) :

                    $result = self::parasut('invoice', 'show', ['include' => 'active_e_document'], $invoice->sales_id);

                    if (!isset($result['included'][0]['id']))
                    
                        return [false, 'Resmi fatura henüz hazır değil.'];
        
                    else
                    {
                        $invoice->e_id = $result['included'][0]['id'];
        
                        $invoice->save();
                    }

                endif;

                if (!$invoice->mail) :

                    $result = self::parasut('invoice', 'pdf_e_'. $invoice->status, null, $invoice->e_id);

                    if (!isset($result['data']['attributes']['url']))

                        return [false, 'Pdf henüz hazır değil.'];

                    try
                    {
                        $path = public_path('e-pdfs/'. mt_rand() .'.pdf');

                        file_put_contents($path, file_get_contents($result['data']['attributes']['url']));

                        $data = ['subj' => 'invoice', 'type' => 'trendyol', 'id' => $invoice->order_id];

                        \Mail::send(['html' => 'mail'], $data, function($msg) use ($path, $order)
                        {
                            $name = $order->customerFirstName .' '. $order->customerLastName;

                            $msg->to($order->customerEmail, $name)->subject('Noone Faturanız - '. date('d.m.Y'));
                                    
                            $msg->from('web@noone.com.tr', 'Noone');
                        
                            $msg->attach($path);
                        });

                        File::delete($path);

                        $invoice->mail = 1;

                        $invoice->save();

                        return [true, 'Başarılı.'];
                    }

                    catch (\Exception $e)
                    {
                        return [false, 'Mail gönderilemedi. '. $e->getMessage()];
                    }

                endif;

            elseif ($invoice->type == 'n11') :

                $result = self::invoice_n11($invoice);

                if (!$result[0])

                    return $result;

                else $order = $result[1];

                if (!$invoice->e_track) :

                    if (!$invoice->status)
                    {
                        $check = self::parasut('invoice', 'checkType', $order->taxNumber);

                        $invoice->status = isset($check['data'][0]['id']) ? 'invoice' : 'archive';

                        $invoice->save();
                    }

                    if ($invoice->status == 'invoice') :

                        $data =

                        '{
                            "data": {
                                "type": "e_invoices",
                                "attributes": {
                                    "note": "N11 '. $order->orderNumber .'",
                                    "scenario": "basic",
                                    "to": "'. $check['data'][0]['e_invoice_address'] .'"
                                },
                                "relationships": {
                                    "invoice": {
                                        "data": {
                                            "id": "'. $invoice->sales_id .'",
                                            "type": "sales_invoices"
                                        }
                                    }
                                }
                            }
                        }';

                    else :

                        $data =

                        '{
                            "data": {
                                "type": "e_archives",
                                "attributes": {
                                    "note": "N11 '. $order->orderNumber .'",
                                    "internet_sale": {
                                        "url": "n11.com",
                                        "payment_type": "ODEMEARACISI",
                                        "payment_platform": "N11",
                                        "payment_date": "'. date('Y-m-d') .'"
                                    },
                                    "shipment": {
                                        "title": "'. $order->itemList->item[0]->shipmentInfo->shipmentCompany->name .'",
                                        "vkn": "'. $order->itemList->item[0]->shipmentInfo->campaignNumber .'",
                                        "date": "'. date('Y-m-d') .'"
                                    }
                                },
                                "relationships": {
                                    "sales_invoice": {
                                        "data": {
                                            "id": "'. $invoice->sales_id .'",
                                            "type": "sales_invoices"
                                        }
                                    }
                                }
                            }
                        }';

                    endif;

                    $result = self::parasut('invoice', 'create_e_'. $invoice->status, $data);

                    if (!isset($result['data']['id']) || 
                        (isset($result['data']['attributes']['status']) && 
                            $result['data']['attributes']['status'] == 'error'))
                        
                        return [false, 'Fatura resmileştirilemedi.'];

                    else
                    {
                        $invoice->e_track = $result['data']['id'];
        
                        $invoice->save();
                    }

                endif;

                if (!$invoice->e_id) :

                    $result = self::parasut('invoice', 'show', ['include' => 'active_e_document'], $invoice->sales_id);

                    if (!isset($result['included'][0]['id']))
                    
                        return [false, 'Resmi fatura henüz hazır değil.'];
        
                    else
                    {
                        $invoice->e_id = $result['included'][0]['id'];
        
                        $invoice->save();
                    }

                endif;

                if (!$invoice->mail) :

                    $result = self::parasut('invoice', 'pdf_e_'. $invoice->status, null, $invoice->e_id);

                    if (!isset($result['data']['attributes']['url']))

                        return [false, 'Pdf henüz hazır değil.'];

                    try
                    {
                        $path = public_path('e-pdfs/'. mt_rand() .'.pdf');

                        file_put_contents($path, file_get_contents($result['data']['attributes']['url']));

                        $data = ['subj' => 'invoice', 'type' => 'n11', 'id' => $invoice->order_id];

                        \Mail::send(['html' => 'mail'], $data, function($msg) use ($path, $order)
                        {
                            $msg->to($order->buyer->email, $order->buyer->fullName)->subject('Noone Faturanız - '. date('d.m.Y'));
                                    
                            $msg->from('web@noone.com.tr', 'Noone');
                        
                            $msg->attach($path);
                        });

                        File::delete($path);

                        $invoice->mail = 1;

                        $invoice->save();

                        return [true, 'Başarılı.'];
                    }

                    catch (\Exception $e)
                    {
                        return [false, 'Mail gönderilemedi. '. $e->getMessage()];
                    }

                endif;

            endif;  
        }

        catch (\Exception $e)
        {
            $result = json_decode($e->getMessage());

            return [false, $result->errors[0]->detail];
        }
    }

    // n11

    public function list_n11(Request $r)
    {
        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        
            $date = explode(' | ', $r->get('date'));

        else
        {
            $date =
            [
                date('d/m/Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
                date('d/m/Y'),
            ];
        }

        $data['datas'] = Controller::n11('OrderService', 'DetailedOrderList',
        [
            'searchData' =>
            [
                'buyerName' => null,
                'orderNumber' => null,
                'productSellerCode' => null,
                'recipient' => null,
                'period' =>
                [
                    'startDate' => str_replace('.', '/', $date[0]),
                    'endDate' => str_replace('.', '/', $date[1])
                ],
                'sortForUpdateDate' => false
            ],
            'pagingData' =>
            [
                'currentPage' => $page - 1,
                'pageSize' => $show
            ]
        ]);

        // dd($data['datas']); exit;

        return view('order.n11', $data);
    }

    public function return_n11(Request $r)
    {
        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        
            $date = explode(' | ', $r->get('date'));

        else
        {
            $date =
            [
                date('d/m/Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
                date('d/m/Y'),
            ];
        }

        $data['datas'] = Controller::n11('ReturnService', 'ClaimReturnList',
        [
            'searchData' =>
            [
                'status' => 'ALL',
                'executer' => 'ALL',
                'searchInfoType' => 'ALL',
                'searchQuery' => null,
                'period' =>
                [
                    'startDate' => str_replace('.', '/', $date[0]),
                    'endDate' => str_replace('.', '/', $date[1])
                ]
            ]
        ]);

        $result = Controller::n11('ReturnService', 'ClaimReturnDenyReasonType');

        if (isset($result->denyReasonTypeDataList))

            $data['reasons'] = $result->denyReasonTypeDataList;

        $result = Controller::n11('ReturnService', 'ClaimReturnPendingReasonType');

        if (isset($result->pendingReasonTypeDataList))

            $data['pendings'] = $result->pendingReasonTypeDataList;

        return view('claim.n11.return', $data);
    }

    public function cancel_n11(Request $r)
    {
        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        
            $date = explode(' | ', $r->get('date'));

        else
        {
            $date =
            [
                date('d/m/Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
                date('d/m/Y'),
            ];
        }

        $data['datas'] = Controller::n11('ClaimCancelService', 'ClaimCancelList',
        [
            'searchData' =>
            [
                'status' => 'ALL',
                'executer' => 'ALL',
                'searchInfoType' => 'ALL',
                'searchQuery' => null,
                'searchDate' =>
                [
                    'searchDateType' => 'REQUESTED',
                    'period' =>
                    [
                        'startDate' => str_replace('.', '/', $date[0]),
                        'endDate' => str_replace('.', '/', $date[1])
                    ]
                ]
            ]
        ]);

        $result = Controller::n11('ClaimCancelService', 'ClaimCancelDenyReasonType');

        if (isset($result->denyReasonTypeDataList))

            $data['reasons'] = $result->denyReasonTypeDataList;

        return view('claim.n11.cancel', $data);
    }

    public function change_n11(Request $r)
    {
        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        
            $date = explode(' | ', $r->get('date'));

        else
        {
            $date =
            [
                date('d/m/Y', strtotime('-2 week', strtotime(date('Y-m-d')))),
                date('d/m/Y'),
            ];
        }

        $data['datas'] = Controller::n11('ClaimExchangeService', 'ClaimExchangeList',
        [
            'searchData' =>
            [
                'status' => 'ALL',
                'executer' => 'ALL',
                'searchInfoType' => 'ALL',
                'searchQuery' => null,
                'period' =>
                [
                    'startDate' => str_replace('.', '/', $date[0]),
                    'endDate' => str_replace('.', '/', $date[1])
                ]
            ]
        ]);

        $result = Controller::n11('ClaimExchangeService', 'ExchangeDenyReasonType');

        if (isset($result->exchangeReasonTypeDataList))

            $data['reasons'] = $result->exchangeReasonTypeDataList;

        $result = Controller::n11('ClaimExchangeService', 'ExchangePendingReasonType');

        if (isset($result->exchangeReasonTypeDataList))

            $data['pendings'] = $result->exchangeReasonTypeDataList;

        $result = Controller::n11('ShipmentCompanyService', 'GetShipmentCompanies');

        if (isset($result->shipmentCompanies->shipmentCompany))
                    
            $data['cargos'] = $result->shipmentCompanies->shipmentCompany;

        return view('claim.n11.change', $data);
    }

    public function bank_n11(Request $r)
    {
        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        if ($r->has('date'))
        {
            $date = explode(' | ', $r->get('date'));

            list($day, $month, $year) = explode('.', $date[0]);

            $date[0] = $year .'-'. $month .'-'. $day;

            list($day, $month, $year) = explode('.', $date[1]);

            $date[1] = $year .'-'. $month .'-'. $day;
        }

        else
        {
            $date =
            [
                date('Y-m-d', strtotime('-2 week', strtotime(date('Y-m-d')))),
                date('Y-m-d'),
            ];
        }

        $data['datas'] = Controller::n11('SapBankStatementEInvoiceService', 'GetSapBankStatementEInvoice',
        [
            'startDate' => str_replace('.', '-', $date[0]),
            'endDate' => str_replace('.', '-', $date[1])
        ]);

        return view('bill.n11', $data);
    }

    public static function invoice_n11(Invoice $invoice)
    {
        $result = Controller::n11('OrderService', 'OrderDetail',
        [
            'orderRequest' => ['id' => $invoice->order_id]
        ]); 

        if (isset($result->orderDetail))
        
            $order = $result->orderDetail;

        else
        {
            echo $invoice->order_id ." n11 den siparis cekilemedi\n";

            return [false, 'N11 sorgu hatası.'];
        }

        $match = [];

        if (!is_array($order->itemList->item))

            $order->itemList->item = [$order->itemList->item];

        foreach ($order->itemList->item as $line) :

            // $line->barcode = '887961428506';

            $temp = Match::join('product', 'product.id', '=', 'match.product_id')
                ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
                ->where('match.type', 'n11')
                ->where('match.code', $line->productSellerCode)
                ->where('product.del', 0)
                ->select('product.*', 'brand.name AS brand')
                ->first();

            if (!$temp)
            {
                echo $line->productSellerCode ." urun eslestirilmemis\n";

                return [false, $line->productSellerCode .' kodlu ürün eşleştirilmemiş.'];
            }

            $match[$line->productSellerCode] = $temp;

        endforeach;

        if (isset($order->citizenshipId))

            $order->taxNumber = $order->citizenshipId ? $order->citizenshipId : '00000000000';

        else $order->taxNumber = '00000000000';

        if (!$invoice->account_id) :

            $data = 
        
            '{
                "data": {
                    "id": "N'. $order->id .'",
                    "type": "contacts",
                    "attributes": {
                        "email": "'. $order->buyer->email .'",
                        "name": "'. $order->billingAddress->fullName .'",
                        "contact_type": "person",
                        "tax_number": "'. $order->taxNumber .'",
                        "district": "'. $order->billingAddress->district .'",
                        "city": "'. $order->billingAddress->city .'",
                        "address": "'. stripslashes($order->billingAddress->address) .'",
                        "is_abroad": false,
                        "archived": false,
                        "account_type": "customer"
                    },
                    "relationships": {
                        "category": {
                            "data": {
                                "id": "5190890",
                                "type": "item_categories"
                            }
                        }
                    }
                }
            }';

            $result = self::parasut('account', 'create', $data);
            
            if (!isset($result['data']['id']))
            {
                echo $invoice->order_id ." musteri parasut e aktarilamadi\n";

                return [false, 'Müşteri kaydedilemedi.'];
            }

            else
            {
                $invoice->account_id = $result['data']['id'];

                $invoice->save();
            }

        endif;

        if (!$invoice->sales_id) :

            $temp = date('Y-m-d');

            $due_date = date('l', strtotime($temp));

            if ($due_date != 'Thursday')
            {
                while ($due_date != 'Thursday')
                {
                    $due_date = date('l', strtotime($temp .' +1 days'));

                    $temp = date('Y-m-d', strtotime($temp .' +1 days'));
                }
            }

            $due_date = date('Y-m-d', strtotime($temp .' +14 days'));

            $data =

            '{
                "data": {
                    "type": "sales_invoices",
                    "attributes": {
                        "item_type": "invoice",
                        "description": "N11 #'. $order->orderNumber .'",
                        "issue_date": "'. date('Y-m-d') .'",
                        "due_date": "'. $due_date .'",
                        "currency": "TRL",
                        "billing_address": "'. stripslashes($order->billingAddress->address) .'",
                        "tax_office": null,
                        "tax_number": "'. $order->taxNumber .'",
                        "city": "'. $order->billingAddress->city .'",
                        "district": "'. $order->billingAddress->district .'",
                        "order_no": "'. $order->orderNumber .'",
                        "order_date": "'. date('Y-m-d') .'",
                        "shipment_addres": "'. stripslashes($order->shippingAddress->address) .'",
                        "shipment_included": true
                    },
                    "relationships": {
                        "details": {
                            "data": [';

                            $temp = [];

                            foreach ($order->itemList->item as $line) :

                                $desc = [$match[$line->productSellerCode]->code, $match[$line->productSellerCode]->brand, $line->productSellerCode];

                                $desc = implode(' ', array_filter($desc));

                                if ($match[$line->productSellerCode]->tax > 0)

                                    $line->sellerInvoiceAmount = $line->sellerInvoiceAmount / (1 + ($match[$line->productSellerCode]->tax / 100));
                                
                                $temp[] =
                                
                                '{
                                    "type": "sales_invoice_details",
                                    "attributes": {
                                        "quantity": '. $line->quantity .',
                                        "unit_price": '. $line->sellerInvoiceAmount .',
                                        "vat_rate": '. $match[$line->productSellerCode]->tax .',
                                        "description": "'. $desc .'"
                                    },
                                    "relationships": {
                                        "product": {
                                            "data": {
                                                "id": "'. $match[$line->productSellerCode]->match .'",
                                                "type": "products"
                                            }
                                        }
                                    }
                                }';
                                
                            endforeach;

                            $data .= implode(',', $temp) .
                        
                            ']
                        },
                        "contact": {
                            "data": {
                                "id": "'. $invoice->account_id .'",
                                "type": "contacts"
                            }
                        },
                        "category": {
                            "data": {
                                "id": "5190891",
                                "type": "item_categories"
                            }
                        },
                        "tags": {
                            "data": [
                                {
                                    "id": "341748",
                                    "type": "tags"
                                },
                                {
                                    "id": "343695",
                                    "type": "tags"
                                }
                            ]
                        }
                    }
                }
            }';

            $result = self::parasut('invoice', 'create', $data);

            if (!isset($result['data']['id']))
            {
                echo $invoice->order_id ." fatura parasut e aktarilamadi\n";
            
                return [false, 'Fatura kaydedilemedi.'];
            }
                
            else
            {
                $invoice->sales_id = $result['data']['id'];

                $invoice->save();
            }

        endif;

        return [true, $order];
    }
}
