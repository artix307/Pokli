<?php

namespace Artanis\GapSap\Payment;

use Artanis\GapSap\Models\GoldSilverHistory;
use Illuminate\Support\Facades\Config;
use Webkul\Payment\Payment\Payment;
use Billplz\Client;
use Illuminate\Support\Facades\DB;

class BillPlz extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'billplz';

    private $billplz;

    public function __construct()
    {
        $this->billplz = Client::make('155994cc-37ea-4c78-9460-1062df930f2c', 'S-b4db8m12r7Te8JmS9O79Rg');
        $this->billplz->useSandbox();
    }
    public static function make()
    {
        return (new self())->billplz;
    }

    public function getRedirectUrl()
    {
        return route('gapsap.redirect');
    }

    public function getFormFields()
    {
        $cart = $this->getCart();
        $billingAddress = $cart->billing_address;
        $item = $this->getCartItems();

        $fields = [
            'collection_id'        => 'x7afhxzc',
            'email'                => $billingAddress->email,
            'name'                 => $billingAddress->first_name.' '.$billingAddress->last_name,
            'amount'               => $cart->grand_total,
            'callback_url'         => route('gapsap.cancel'),
            'description'          => 'Testing API',
            'redirect_url'         => route('gapsap.redirect'),
            'reference_1_label'    => 'Item : ',
            'reference_1'          => core()->getCurrentChannel()->name
        ];

        return $fields;
    }

    // public function getBillPlzlUrl($params = [])
    // {
    //     $purchase = GoldSilverHistory::where('customer_id',auth()->guard('customer')->user()->id)->latest()->first();
    //     // dd($purchase->customer->email);
    //
    //     // $cart = $this->getCart();
    //     // $billingAddress = $cart->billing_address;
    //     // $item = $this->getCartItems();
    //     // dd($cart);
    //
    //     $billplzCreate = Client::make('9e044b22-afda-4245-ba20-a9c4249d5cc2', 'S-Fq-j4wtQghYPQ80vd0kjbw');
    //     $bill = $billplzCreate->bill();
    //     $response = $bill->create(
    //         'wf6m9pmq', //collection id
    //         $purchase->customer->email, //user email
    //         null,
    //         $purchase->customer->first_name.' '.$purchase->customer->last_name, //user name
    //         \Duit\MYR::given($purchase->purchase_amount*100), //total price
    //         ['callback_url' => route('gapsap.verify'), 'redirect_url' => route('gapsap.verify')], //url
    //         $purchase->increment_id
    //     );
    //     $responseArray = $response->toArray();
    //     $url = $responseArray['url'];
    //     return redirect()->away($url);
    //   // return 'https://billplz-staging.herokuapp.com/bills/'.$id;
    // }

    public function getBillPlzlUrl($params = [])
    {
        $purchase = GoldSilverHistory::where('customer_id',auth()->guard('customer')->user()->id)->latest()->first();
        // dd($purchase->customer->email);

        // $cart = $this->getCart();
        // $billingAddress = $cart->billing_address;
        // $item = $this->getCartItems();
        // dd($cart);

        $billplzCreate = Client::make('155994cc-37ea-4c78-9460-1062df930f2c', 'S-b4db8m12r7Te8JmS9O79Rg')->useSandbox();
        $bill = $billplzCreate->bill();
        $response = $bill->create(
            'wf6m9pmq', //collection id
            $purchase->customer->email, //user email
            null,
            $purchase->customer->first_name.' '.$purchase->customer->last_name, //user name
            \Duit\MYR::given($purchase->purchase_amount*100), //total price
            ['callback_url' => route('gapsap.verify'), 'redirect_url' => route('gapsap.verify')], //url
            $purchase->increment_id
        );
        $responseArray = $response->toArray();
        $url = $responseArray['url'];
        // return redirect()->away($url);
        return $url;
      // return 'https://billplz-staging.herokuapp.com/bills/'.$id;
    }
}
