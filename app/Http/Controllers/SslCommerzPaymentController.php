<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Library\SslCommerz\SslCommerzNotification;
use App\AppModels\Address;
use Illuminate\Support\Facades\Validator;
use App\AppModels\Product;
use App\AppModels\OrderMaster;
use App\AppModels\OrderProductAttribute;
use App\AppModels\ProductAttribute;
use App\AppModels\ProductAttributeValue;
use App\AppModels\Order;


class SslCommerzPaymentController extends Controller {

  public function __construct() {
      $this->middleware('IPValidation')->only('ipn');
  }

  public function exampleEasyCheckout() {
      return view('exampleEasycheckout');
  }

  public function exampleHostedCheckout() {
      return view('exampleHosted');
  }

  public function index(Request $request) {
    // validate cart
    //redirect customer id cart is empty.
    if (!isset(session()->get('cart')[0]['product_id'])) {
      return redirect('/cart')->withFail('Cart is empty!');
    }

    // get customer address & shipping address
    $cust_shipp_address = Address::where('user_id', auth()->user()->id ?? '')->find(session()->get('shipping_address_id'));
    if (!$cust_shipp_address) {
      return redirect('/cart')->withFail('Shipping address not found!');
    }

    // validate address data
    $input = [
      'name' => (auth()->user()->first_name ?? '') .' '. (auth()->user()->last_name ?? ''),
      'email' => auth()->user()->email ?? '',
      'address' => $cust_shipp_address->address_1 ?? '',
      'city' => $cust_shipp_address->city ?? '',
      'state' => $cust_shipp_address->state ?? '',
      'postcode' => $cust_shipp_address->zip ?? '',
      'country' => $cust_shipp_address->country ?? '',
      'mobile' => $cust_shipp_address->mobile ?? '',
    ];
    $validator = Validator::make($input, [
        'name' => 'required|max:255',
        'email' => 'required|max:255',
        'address' => 'required|max:255',
        'city' => 'required|max:255',
        'state' => 'required|max:255',
        'postcode' => 'required|max:255',
        'country' => 'required|max:255',
        'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:11',
    ]);
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    // get validated data
    $d = $validator->valid();


    /* Calculate total */
    // get cart from session
    $carts = session()->get('cart');

    // total cost
    $totals = array('total_price' => 0, 'discount' => 0, 'total' => 0);
    $product_ids = array();
    if(is_array($carts)){
      foreach ($carts as $cart) {
        array_push($product_ids, $cart['product_id']);
      }
    }
    $products = Product::find($product_ids);

    foreach ($products as $k => $product) {
      $totals['total_price'] += $product->priceAfterDiscount() * $carts[$k]['qty'];
    }
    foreach ($products as $k => $product) {
      $totals['discount'] += ($product->sale_price - $product->priceAfterDiscount());
    }
    $totals['total'] = $totals['total_price'] - $totals['discount'];

    # Here you have to receive all the order data to initate the payment.
    # Let's say, your oder transaction informations are saving in a table called "orders"
    # In "orders" table, order unique identity is "transaction_id".
    #"status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
    $post_data = array();
    $post_data['total_amount'] = round(ceil($totals['total']*1000)/1000,2); # You cant not pay less than 10
    $post_data['currency'] = "BDT";
    $post_data['tran_id'] = uniqid(); // tran_id must be unique


    # CUSTOMER INFORMATION
    $post_data['cus_name'] = $d['name'] ?? '';
    $post_data['cus_email'] = $d['email'] ?? '';
    $post_data['cus_add1'] = $d['address'] ?? '';
    $post_data['cus_add2'] = "";
    $post_data['cus_city'] = $d['city'] ?? '';
    $post_data['cus_state'] = $d['state'] ?? '';
    $post_data['cus_postcode'] = $d['postcode'] ?? '';
    $post_data['cus_country'] = $d['country'] ?? '';
    $post_data['cus_phone'] = $d['mobile'] ?? '';
    $post_data['cus_fax'] = "";

    # SHIPMENT INFORMATION
    $post_data['ship_name'] = "Store Test";
    $post_data['ship_add1'] = $d['address'] ?? '';
    $post_data['ship_add2'] = "";
    $post_data['ship_city'] = $d['city'] ?? '';
    $post_data['ship_state'] = $d['state'] ?? '';
    $post_data['ship_postcode'] = $d['postcode'] ?? '';
    $post_data['ship_phone'] = $d['mobile'];
    $post_data['ship_country'] = $d['country'] ?? '';

    $post_data['shipping_method'] = "NO";
    $post_data['product_name'] = $products[0]->title ?? 'Not found';
    $post_data['product_category'] = $products[0]->category->name ?? 'Not found';
    $post_data['product_profile'] = "physical-goods";

    # OPTIONAL PARAMETERS
    $post_data['value_a'] = "ref001";
    $post_data['value_b'] = "ref002";
    $post_data['value_c'] = "ref003";
    $post_data['value_d'] = "ref004";

    #Before  going to initiate the payment order status need to insert or update as Pending.
    $update_product = DB::table('sslcommerz_transactions')
        ->where('transaction_id', $post_data['tran_id'])
        ->updateOrInsert([
            'name' => $post_data['cus_name'],
            'order_master_id' => NULL,
            'email' => $post_data['cus_email'],
            'phone' => $post_data['cus_phone'],
            'amount' => $post_data['total_amount'],
            'status' => 'Pending',
            'address' => $post_data['cus_add1'],
            'transaction_id' => $post_data['tran_id'],
            'currency' => $post_data['currency']
        ]);

    $sslc = new SslCommerzNotification();
    # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
    $payment_options = $sslc->makePayment($post_data, 'hosted');

    if (!is_array($payment_options)) {
        print_r($payment_options);
        $payment_options = array();
    }

  }

  public function payViaAjax(Request $request) {

      return 'Access denied!';

      # Here you have to receive all the order data to initate the payment.
      # Lets your oder trnsaction informations are saving in a table called "orders"
      # In orders table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

      $post_data = array();
      $post_data['total_amount'] = '10'; # You cant not pay less than 10
      $post_data['currency'] = "BDT";
      $post_data['tran_id'] = uniqid(); // tran_id must be unique

      # CUSTOMER INFORMATION
      $post_data['cus_name'] = 'Customer Name';
      $post_data['cus_email'] = 'customer@mail.com';
      $post_data['cus_add1'] = 'Customer Address';
      $post_data['cus_add2'] = "";
      $post_data['cus_city'] = "";
      $post_data['cus_state'] = "";
      $post_data['cus_postcode'] = "";
      $post_data['cus_country'] = "Bangladesh";
      $post_data['cus_phone'] = '8801XXXXXXXXX';
      $post_data['cus_fax'] = "";

      # SHIPMENT INFORMATION
      $post_data['ship_name'] = "Store Test";
      $post_data['ship_add1'] = "Dhaka";
      $post_data['ship_add2'] = "Dhaka";
      $post_data['ship_city'] = "Dhaka";
      $post_data['ship_state'] = "Dhaka";
      $post_data['ship_postcode'] = "1000";
      $post_data['ship_phone'] = "";
      $post_data['ship_country'] = "Bangladesh";

      $post_data['shipping_method'] = "NO";
      $post_data['product_name'] = "Computer";
      $post_data['product_category'] = "Goods";
      $post_data['product_profile'] = "physical-goods";

      # OPTIONAL PARAMETERS
      $post_data['value_a'] = "ref001";
      $post_data['value_b'] = "ref002";
      $post_data['value_c'] = "ref003";
      $post_data['value_d'] = "ref004";


      #Before  going to initiate the payment order status need to update as Pending.
      $update_product = DB::table('sslcommerz_transactions')
          ->where('transaction_id', $post_data['tran_id'])
          ->updateOrInsert([
              'name' => $post_data['cus_name'],
              'email' => $post_data['cus_email'],
              'phone' => $post_data['cus_phone'],
              'amount' => $post_data['total_amount'],
              'status' => 'Pending',
              'address' => $post_data['cus_add1'],
              'transaction_id' => $post_data['tran_id'],
              'currency' => $post_data['currency']
          ]);

      $sslc = new SslCommerzNotification();
      # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
      $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

      if (!is_array($payment_options)) {
          print_r($payment_options);
          $payment_options = array();
      }

  }

  public function success(Request $request) {
      $tran_id = $request->input('tran_id');
      $amount = $request->input('amount');
      $currency = $request->input('currency');

      $sslc = new SslCommerzNotification();

      #Check order status in order tabel against the transaction id or order id.
      $order_detials = DB::table('sslcommerz_transactions')
          ->where('transaction_id', $tran_id)
          ->select('transaction_id', 'status', 'currency', 'amount')->first();

      if ($order_detials->status == 'Pending') {
          $validation = $sslc->orderValidate($tran_id, $amount, $currency, $request->all());

          if ($validation == TRUE) {
              /*
              That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
              in order table as Processing or Complete.
              Here you can also sent sms or email for successfull transaction to customer
              */
              $update_product = DB::table('sslcommerz_transactions')
                  ->where('transaction_id', $tran_id)
                  ->update(['status' => 'Processing']);

              // echo "<br >Transaction is successfully Completed";

              // completed the order
              return $this->completeTheOrder($request);

          } else {
              /*
              That means IPN did not work or IPN URL was not set in your merchant panel and Transation validation failed.
              Here you need to update order status as Failed in order table.
              */
              $update_product = DB::table('sslcommerz_transactions')
                  ->where('transaction_id', $tran_id)
                  ->update(['status' => 'Failed']);
              // echo "validation Fail";
              return redirect('/cart')->withFail("Order has been failed.");
          }

          } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
              /*
               That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
               */
              // echo "Transaction is successfully Completed";
              return $this->completeTheOrder($request);
          } else {
              #That means something wrong happened. You can redirect customer to your product page.
              // echo "Invalid Transaction";
              return redirect('/cart')->withFail("Invalid Transaction.");
          }

  }

  public function fail(Request $request) {
      $tran_id = $request->input('tran_id');

      $order_detials = DB::table('sslcommerz_transactions')
          ->where('transaction_id', $tran_id)
          ->select('transaction_id', 'status', 'currency', 'amount')->first();

      if ($order_detials->status == 'Pending') {
          $update_product = DB::table('sslcommerz_transactions')
              ->where('transaction_id', $tran_id)
              ->update(['status' => 'Failed']);
          // echo "Transaction is Falied";
          return redirect('/cart')->withFail("Transaction has failed");

      } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
          // echo "Transaction is already Successful";
          return redirect('/user')->withSuccess("Order is already successful");

      } else {
          // echo "Transaction is Invalid";
          return redirect('/cart')->withFail("Transaction is invalid");
      }

  }

  public function cancel(Request $request) {
      $tran_id = $request->input('tran_id');

      $order_detials = DB::table('sslcommerz_transactions')
          ->where('transaction_id', $tran_id)
          ->select('transaction_id', 'status', 'currency', 'amount')->first();

      if ($order_detials->status == 'Pending') {
          $update_product = DB::table('sslcommerz_transactions')
              ->where('transaction_id', $tran_id)
              ->update(['status' => 'Canceled']);
          // echo "Transaction is Cancel";
          return redirect('/cart')->withFail("Transaction has canceled");

      } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
          // echo "Transaction is already Successful";
          return redirect('/user')->withSuccess("Order is already successful");
      } else {
          // echo "Transaction is Invalid";
          return redirect('/cart')->withFail("Transaction is invalid");
      }


  }

  public function ipn(Request $request) {
      #Received all the payement information from the gateway
      if ($request->input('tran_id')) #Check transation id is posted or not.
      {

          $tran_id = $request->input('tran_id');

          #Check order status in order tabel against the transaction id or order id.
          $order_details = DB::table('sslcommerz_transactions')
              ->where('transaction_id', $tran_id)
              ->select('transaction_id', 'status', 'currency', 'amount')->first();

          if ($order_details->status == 'Pending') {
              $sslc = new SslCommerzNotification();
              $validation = $sslc->orderValidate($tran_id, $order_details->amount, $order_details->currency, $request->all());
              if ($validation == TRUE) {
                  /*
                  That means IPN worked. Here you need to update order status
                  in order table as Processing or Complete.
                  Here you can also sent sms or email for successful transaction to customer
                  */
                  $update_product = DB::table('sslcommerz_transactions')
                      ->where('transaction_id', $tran_id)
                      ->update(['status' => 'Processing']);

                  echo "Transaction is successfully Completed";
              } else {
                  /*
                  That means IPN worked, but Transation validation failed.
                  Here you need to update order status as Failed in order table.
                  */
                  $update_product = DB::table('sslcommerz_transactions')
                      ->where('transaction_id', $tran_id)
                      ->update(['status' => 'Failed']);

                  echo "validation Fail";
              }

          } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {

              #That means Order status already updated. No need to udate database.

              echo "Transaction is already successfully Completed";
          } else {
              #That means something wrong happened. You can redirect customer to your product page.

              echo "Invalid Transaction";
          }
      } else {
          echo "Invalid Data";
      }
  }


  // our custom method to complete the order
  private function completeTheOrder($request) {
    // our custom code
    // Insert user order details

    // total qty
    $total_qty = 0;
    $carts = session()->get('cart');
    foreach ($carts as $cart) {
      $total_qty += $cart['qty'];
    }


    // total cost
    $totals = array('total_price' => 0, 'discount' => 0, 'total' => 0);
    $product_ids = array();
    if(is_array($carts)){
      foreach ($carts as $cart) {
        array_push($product_ids, $cart['product_id']);
      }
    }
    $products = Product::find($product_ids);

    foreach ($products as $k => $product) {
      $totals['total_price'] += $product->priceAfterDiscount() * $carts[$k]['qty'];
    }
    foreach ($products as $k => $product) {
      $totals['discount'] += ($product->sale_price - $product->priceAfterDiscount());
    }
    $totals['total'] = $totals['total_price'] - $totals['discount'];


    // create order master record
    // flag for storing
    $insert_success = false;
    $om = new OrderMaster();
    $om->user_id = auth()->user()->id;
    $om->shpping_address_id = session()->get('shipping_address_id');
    $om->total_qty = $total_qty;
    $om->sub_total = $totals['total_price'];
    $om->discount_total = $totals['discount'];
    $om->total_price = round(ceil($totals['total']*1000)/1000,2);
    $insert_success = $om->save();


    // create order for a single product
    if ($insert_success) {
      foreach ($products as $k => $product) {
        $order = new Order();
        $order->order_master_id = $om->id;
        $order->product_id = $product->id;
        $order->qty = $carts[$k]['qty'];
        $order->unit_sale_price = $product->sale_price;
        $order->subtotal_price = $product->sale_price * $carts[$k]['qty'];
        $order->has_discount = $product->has_discount;
        $order->discount_type = $product->discount_type;
        $order->discount_fixed_price = $product->discount_fixed_price;
        $order->discount_percent = $product->discount_percent;
        $order->total_sale_price = $product->priceAfterDiscount() * $carts[$k]['qty'];
        $insert_success = $order->save();

        // attributes & attribute values
        foreach ($carts[$k]['attribute_value_ids'] as $value_id) {
          $attribute_value = ProductAttributeValue::findOrFail($value_id);
          $opa = new OrderProductAttribute();
          $opa->order_id = $order->id;
          $opa->product_attribute_id = $attribute_value->product_attribute_id;
          $opa->product_attribute_value_id = $value_id;
          $insert_success = $opa->save();
        }

      }
    }
    // end order

    // finalize
    if ($insert_success) {
      $tran_id = $request->input('tran_id');

      $data = array();
      $data['card_type'] = $request->input('card_type');
      $data['store_amount'] = $request->input('store_amount');
      $data['card_no'] = $request->input('card_no');
      $data['bank_tran_id'] = $request->input('bank_tran_id');
      $data['error'] = $request->input('error');
      $data['card_issuer'] = $request->input('card_issuer');
      $data['card_brand'] = $request->input('card_brand');
      $data['card_issuer_country'] = $request->input('card_issuer_country');
      $data['currency_amount'] = $request->input('currency_amount');
      $data['currency_rate'] = $request->input('currency_rate');
      $data['risk_title'] = $request->input('risk_title');
      $data['order_master_id'] = $om->id;



      // insert master_transaction_id
      $update_product = DB::table('sslcommerz_transactions')
          ->where('transaction_id', $tran_id)
          ->update($data);

      // clear cart session
      session()->put('cart', array());
      session()->put('shipping_address_id', '');
      session()->save();
      return redirect('/user')->withSuccess("Order has been completed.");
    }
  }


}
