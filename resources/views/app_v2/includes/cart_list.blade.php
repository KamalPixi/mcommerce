<div class="dropdown-item cart-title">
  <h6 class="mb-0">Cart Items</h6>
</div>
<div class="dropdown-item" style="width: 20rem"></div>

@foreach($products as $key => $product)
<div class="dropdown-item cart-element-wrapper pb-2 mb-2 border-bottom">
  <div class="cart-element ow">
    <div class="">
      <img src="{{ asset('storage/product_thumbs') }}/{{ $product->thumbnail->thumbnail ?? '' }}" alt="" width="50">
    </div>
    <div class="px-3 title">
      <a href="#" class="d-block">{{ $product->title ?? '' }}</a>
      <!-- price after discount -->
      @if($product->has_discount)
        <small class="muted d-inline-block pt-2">&#2547;{{ number_format($product->priceAfterDiscount(), 2) }} x {{ $carts[$key]['qty'] ?? 1 }}</small>
      @else
        <small class="muted d-inline-block pt-2">&#2547;{{ number_format($product->sale_price, 2) }} x {{ $carts[$key]['qty'] ?? 1 }}</small>
      @endif
    </div>
    <div class="">
      <button type="button" name="button" class="border-0 bg-transparent" onclick="removeFromCart({{ $product->id ?? '' }})"><i class="fas fa-times"></i></button>
    </div>
  </div>
</div>
@endforeach

@if(isset($carts) && count($carts) > 0)
<div class="dropdown-item text-center">
  <a href="/cart" class="btn btn-primary-theme px-5" type="button">Cart <i class="fas fa-shopping-cart" style="font-size: 1rem;"></i></a>
</div>
@else
  <p class="text-muted ml-3 pl-1">Cart is Empty.</p>
@endif
