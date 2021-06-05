<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderC;

use App\Models\Invoice;

class AutoStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic Stock Control';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        require_once(app_path('Http/Google/api/vendor/autoload.php'));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->control_trendyol();

        $this->control_n11();

        OrderC::auto_stock();

        $this->info('Stock control completed.');
    }

    public function control_trendyol()
    {
        $date =
        [
            date('Y-m-d H:i:s', strtotime('-2 day', strtotime(date('Y-m-d H:i:s')))),
            date('Y-m-d H:i:s', strtotime('+1 hour', strtotime(date('Y-m-d H:i:s')))),
        ];

        $i = 0;

        repeat:

        $param =
        [
            'dateType' => 'Process',
            'startDate' => strtotime($date[0]) * 1000,
            'endDate' => strtotime($date[1]) * 1000,
            'size' => 200, 
            'page' => $i,
        ];

        $result = Controller::trendyol('GET', 'settlements', $param);

        if (!isset($result->errors) && count($result->content[0]->settlementItems) > 0)
        { 
            $items = $result->content[0]->settlementItems;

            foreach ($items as $item) :

                $invoice = Invoice::where('type', 'trendyol')
                    ->where('order_id', $item->orderNumber)
                    ->first();

                if ($item->transactionTypeName == 'Sale') :

                    if (!$invoice)

                        $invoice = new Invoice;

                    if (!$invoice->account_id || !$invoice->sales_id) :

                        $invoice->type = 'trendyol';

                        $invoice->order_id = $item->orderNumber;

                        $invoice->save();

                        $result_i = OrderC::invoice_trendyol($invoice);

                        if (!$result_i[0])

                            $invoice->delete();

                    endif;

                elseif ($item->transactionTypeName == 'Return' || $item->transactionTypeName == 'Cancel') :

                    if ($invoice) :

                        if (!$invoice->e_track) :

                            if ($invoice->sales_id) :
                            
                                $result_i = Controller::parasut('invoice', 'delete', null, $invoice->sales_id);
                    
                                if (isset($result_i['errors']))
                                {
                                    echo $invoice->sales_id ." fatura parasut ten silinemedi\n";

                                    continue;
                                }

                            endif;

                            if ($invoice->account_id) :

                                $result_i = Controller::parasut('account', 'delete', null, $invoice->account_id);
                
                                if (isset($result_i['errors']))
                                {
                                    echo $invoice->sales_id ." musteri parasut ten silinemedi\n";

                                    continue;
                                }

                            endif;

                            $invoice->delete();
                        
                        endif;
                    
                    endif;
        
                endif;

            endforeach;

            if ($i < $result->totalPages - 1)
            {
                $i++;

                goto repeat;
            }
        }
    }

    public function control_n11()
    {
        $date =
        [
            date('d/m/Y', strtotime('-2 day', strtotime(date('Y-m-d')))),
            date('d/m/Y', strtotime('+1 hour', strtotime(date('Y-m-d')))),
        ];

        $i = 0;

        repeat:

        $param =
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
                'currentPage' => $i,
                'pageSize' => 100
            ]
        ];

        $result = Controller::n11('OrderService', 'DetailedOrderList', $param);

        if (isset($datas->orderList->order))
        { 
            if (!is_array($datas->orderList->order)) 

                $datas->orderList->order = [$datas->orderList->order];

            foreach ($datas->orderList->order as $item) :

                $invoice = Invoice::where('type', 'n11')
                    ->where('order_id', $item->id)
                    ->first();

                if ($item->status == 1 || $item->status == 2 || $item->status == 5) :

                    if (!$invoice)

                        $invoice = new Invoice;

                    if (!$invoice->account_id || !$invoice->sales_id) :

                        $invoice->type = 'n11';

                        $invoice->order_id = $item->id;

                        $invoice->save();

                        $result_i = OrderC::invoice_n11($invoice);

                        if (!$result_i[0])

                            $invoice->delete();

                    endif;

                elseif ($item->status == 3 || $item->status == 4) :

                    if ($invoice) :

                        if (!$invoice->e_track) :

                            if ($invoice->sales_id) :
                            
                                $result_i = Controller::parasut('invoice', 'delete', null, $invoice->sales_id);
                    
                                if (isset($result_i['errors']))
                                {
                                    echo $invoice->sales_id ." fatura parasut ten silinemedi\n";

                                    continue;
                                }

                            endif;

                            if ($invoice->account_id) :

                                $result_i = Controller::parasut('account', 'delete', null, $invoice->account_id);
                
                                if (isset($result_i['errors']))
                                {
                                    echo $invoice->sales_id ." musteri parasut ten silinemedi\n";

                                    continue;
                                }

                            endif;

                            $invoice->delete();
                        
                        endif;
                    
                    endif;
        
                endif;

            endforeach;

            if (isset($datas->pagingData->pageCount) && $i < $result->pagingData->pageCount - 1)
            {
                $i++;

                goto repeat;
            }
        }
    }
}
