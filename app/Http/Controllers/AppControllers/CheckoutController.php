<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HelperClasses\Helper;
use App\AppModels\Product;
use App\AppModels\ShippingMethod;
use App\AppModels\Address;
use App\AppModels\OrderMaster;
use App\AppModels\OrderProductAttribute;
use App\AppModels\ProductAttribute;
use App\AppModels\ProductAttributeValue;
use App\AppModels\Order;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller {

    public function index() {
      // redirect to cart if cart is empty
      // if (!isset(session()->get('cart')[0]['product_id'])) {
      //   return redirect()->route('cart')->withFail('Cart is empty!');
      // }

      // get product id from session
      $carts = session()->get('cart');
      $product_ids = array();
      if(is_array($carts)){
        foreach ($carts as $cart) {
          array_push($product_ids, $cart['product_id']);
        }
      }
      $products = Product::find($product_ids);

      // get shipping methods details
      $shipping_method_id = session()->get('shipping_method_id');
      $shipping_method = ShippingMethod::find($shipping_method_id);
      // if (!$shipping_method) {
      //   return redirect()->route('cart')->withFail('Please choose a shipping method.');
      // }

      return view('app.checkout.checkout', compact('products', 'shipping_method'));
    }



    public function create() {

    }



    public function store(Request $request) {
      // redirect to cart, if cart is empty
      if (!isset(session()->get('cart')[0]['product_id'])) {
        return redirect()->route('cart')->withFail('Cart is empty!');
      }

      // total qty
      $total_qty = 0;
      $items = session()->get('cart');
      foreach ($items as $item) {
        $total_qty += $item['qty'];
      }

      // total cost
      $total_cost = 0;
      $product_ids = array();
      if(is_array($items)){
        foreach ($items as $item) {
          array_push($product_ids, $item['product_id']);
        }
      }
      $products = Product::find($product_ids);
      foreach ($products as $k => $product) {
        $total_cost += $product->priceAfterDiscount() * $items[$k]['qty'];
      }

      // flag for storing
      $insert_success = false;
      // create order master record
      $om = new OrderMaster();
      $om->user_id = auth()->user()->id;
      $om->shipping_method_id = $shipping_method_id;
      $om->shpping_address_id = $address_ship->id;
      $om->total_qty = $total_qty;
      $om->shipping_cost = $shipping_method->cost;
      $om->total_price = round($total_cost, 2);
      $insert_success = $om->save();

      // create order for a single product
      if ($insert_success) {
        foreach ($products as $k => $product) {
          $order = new Order();
          $order->order_master_id = $om->id;
          $order->product_id = $product->id;
          $order->qty = $items[$k]['qty'];
          $order->unit_sale_price = $product->sale_price;
          $order->subtotal_price = $product->sale_price * $items[$k]['qty'];
          $order->has_discount = $product->has_discount;
          $order->discount_type = $product->discount_type;
          $order->discount_fixed_price = $product->discount_fixed_price;
          $order->discount_percent = $product->discount_percent;
          $order->total_sale_price = $product->priceAfterDiscount() * $items[$k]['qty'];
          $insert_success = $order->save();


          // attributes & attribute values
          foreach ($items[$k]['att_value_ids'] as $value_id) {
            $attribute_value = ProductAttributeValue::findOrFail($value_id);
            $opa = new OrderProductAttribute();
            $opa->order_id = $order->id;
            $opa->product_attribute_id = $attribute_value->product_attribute_id;
            $opa->product_attribute_value_id = $value_id;
            $insert_success = $opa->save();
          }

        }
      }

      if ($insert_success) {
        // clear cart session
        session()->put('cart', array());
        session()->save();
        return response(['error'=>false,'message'=>'Order has been completed.'], 200);
      }
      else {
        return response(['error'=>true, 'message'=>"Failed! Cant't Complete order."], 200);
      }
    }



    public function show($id)
    {

    }



    public function edit($id) {

    }



    public function update(Request $request, $id) {


    }



    public function destroy($id){

    }

    private function getPriceFromPercentage($price, $percent){
      if ($percent > 0) return round($price/100*$percent, 2);
      else return 0;
    }



}
