<?php
namespace App\HelperClasses;
use App\AppModels\Product;

class Helper {

  public static function getCategories($p) {
    $c = array('', '');

    $s = $p->subCategory($p->category_id);
    if ($s) {
      $ss = $p->subSubCategory($s->id);
      if($ss) $c[1] = $ss->name;
      $c[0] = $s->name;
    }

    return $c;
  }

  // removes item from cart by product id.
  public static function removeFromCart($product_id, $request) {
    $cart_old = session()->get('cart');
    $cart_new = array();

    foreach ($cart_old as $cart) {
      if ($cart['product_id'] == $product_id) {
        continue;
      }else {
        array_push($cart_new, $cart);
      }
    }


    session()->put('cart', $cart_new);
    session()->save();

    return array('msg' => 'Item has been removed');
  }


  public static function updateCart($data) {
    $cart_old = session()->get('cart');
    for($i=0; $i < count($cart_old); $i++) {
      session()->get('cart')[$i]['qty'] = $data['qty'][$i];
      // $cart_old[$i]['qty'] = $data['qty'][$i];
    }
    session()->save();

    return redirect()->back()->withSuccess("Cart has been updated.");
  }

}
