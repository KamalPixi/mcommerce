<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\HelperClasses\Helper;
use App\AppModels\Product;
use App\AppModels\Address;
use App\AppModels\ProductAttributeValue;
use App\AppModels\ShippingMethod;

class CartController extends Controller {

    public function cart(Request $request) {



      /* delete cart */
      if ($request->delete) {
        $d = $request->validate(['delete' => 'bail|required|numeric|min:1|gt:0']);
        return Helper::removeFromCart($d['delete'], $request);
      }

      /* store shipping address */
      if ($request->shipping_address) {
        $d = $request->validate([
          'address_id' => 'nullable|numeric|min:1|gt:0|exists:addresses,id',
          'full_name' => 'required|max:255',
          'phone_number' => 'required|max:15',
          'address' => 'required|max:255',
          'division' => 'required|max:255',
          'city' => 'required|max:255',
          'postal_code' => 'required|max:6',
          'country' => 'required|max:11',
        ]);

        // find address or create new one
        $address = "";
        $update_address = false;
        if (isset($d['address_id']) && !empty($d['address_id'])) {
          $address = Address::findOrFail($d['address_id']);
          $update_address = true;
        }else {
          $address = new Address();
        }

        $address->full_name = $d['full_name'];
        $address->user_id = auth()->user()->id;
        $address->mobile = $d['phone_number'];
        $address->type = 'shipping';
        $address->address_1 = $d['address'];
        $address->state = $d['division'];
        $address->city = $d['city'];
        $address->zip = $d['postal_code'];
        $address->country = $d['country'];
        if ($update_address) {
          if ($address->update()) {
            session()->put('shipping_address_id', $address->id);
            session()->save();
            return ['status'=>true];
          }
        }else {
          if ($address->save()) {
            session()->put('shipping_address_id', $address->id);
            session()->save();
            return ['status'=>true];
          }
        }

        return ['status'=>false];
      }
      /* end store shipping address */


      /* cart storing */
      $carts = session()->get('cart');
      $product_ids = array();
      if(is_array($carts)){
        foreach ($carts as $cart) {
          array_push($product_ids, $cart['product_id']);
        }
      }

      // get cart products from db
      $products = Product::find($product_ids);

      // Totals
      $totals = array('total_price' => 0, 'discount' => 0, 'total' => 0);
      foreach ($products as $k => $product) {
        $totals['total_price'] += $product->priceAfterDiscount() * $carts[$k]['qty'];
      }
      foreach ($products as $k => $product) {
        $totals['discount'] += ($product->sale_price - $product->priceAfterDiscount());
      }
      $totals['total'] = $totals['total_price'] - $totals['discount'];

      // get this user all addresses
      $addresses = Address::where('user_id', auth()->user()->id ?? '')->get();
      // convert addresses to json to use in frontend
      $addresses_json = $addresses->toJson();

      // get user set address from session
      $session_address_id = session()->get('shipping_address_id');
      if ($session_address_id) {
        $session_address = Address::find($session_address_id);
      }

      return view('app_v2.cart', compact('products', 'carts', 'totals', 'addresses', 'addresses_json', 'session_address'));
    }


    // add in cart
    public function addToCart(Request $request) {
      // update cart. if request made for updating an item from cart
      if ($request->update) {
        $d = $request->validate([
          'update' => 'required|min:4|max:4',
          'qty' => 'required|array',
          'qty.*' => 'bail|required|numeric|min:1|gt:0'
        ]);

        $cart_old = session()->get('cart');
        for($i=0; $i < count($cart_old); $i++) {
          $cart_old[$i]['qty'] = $d['qty'][$i];
        }
        session()->put('cart', $cart_old);
        session()->save();

        return redirect()->back()->withSuccess("Cart has been updated.");
      }

      // validate cart inputs
      $d = $request->validate([
        'product_id' => 'bail|required|numeric|min:1|gt:0|exists:products,id',
        'qty' => 'bail|nullable|numeric|min:1|gt:0',
        'attribute_value_ids' => 'nullable|array',
        'attribute_value_ids.*' => 'bail|nullable|numeric|min:1|distinct|gt:0'
      ]);


      // get all items from cart session
      $carts = session()->get('cart') ? session()->get('cart') : array();
      // cart limit size is  100
      if (count($carts) > 99) {
        return array("msg"=>"Cart Limit has reached!");
      }

      // filter cart, if already same product id exists, replace with new one just came
      $found = false;
      foreach ($carts as $key => $cart) {
        if ($cart['product_id'] == $d['product_id']) {
          $carts[$key] = $d;
          $found = true;
          break;
        }
      }

      // insert new item in carts
      if (!$found) {
        $cart = array('product_id'=>0, 'qty' => 0, 'attribute_value_ids' => array());
        $cart['product_id'] = $d['product_id'];
        $cart['qty'] = $d['qty'];
        $cart['attribute_value_ids'] = $d['attribute_value_ids'] ?? array();
        array_push($carts, $cart);
      }

      // insert carts in session
      session(['cart' => $carts]);
      // get product ids from cart session
      $product_ids = array();
      foreach ($carts as $cart) {
        array_push($product_ids, $cart['product_id']);
      }
      // fetch products from db, to send back to frontend
      $products = Product::find($product_ids);
      if ($request->ajax()) {
        return response()->json([
          'cart' => view('app_v2.includes.cart_list', compact('products', 'carts'))->render(),
          'cart_size' => count($carts) ?? 0
        ]);
      }

      return redirect()->back()->withSuccess("Item has been added.");
    }
}
