<section class="section-cart my-2rem px-1rem">
  <div class="container-fluid">

    <div class="row">
      <div class="col-md-9 pr-md-0">
        <div class="container-fluid bg-white border">
          <div class="row text-muted section-cart__head pt-4">
            <div class="col-md-6 text-uppercase pl-3 pl-md-4">Product</div>
            <div class="col-md-2 text-uppercase">Quantity</div>
            <div class="col-md-2 text-uppercase">Price</div>
            <div class="col-md-2 text-uppercase"></div>
          </div>
          <hr>

          @if(isset($carts) && count($carts) > 0)
            @foreach($products as $k => $product)
              <form class="" action="cart" method="post">
              @csrf
              <input type="hidden" name="update" value="true">
              <!-- item row -->
              <div class="row mb-3">
                <!-- item description -->
                <div class="col-md-6">
                  <div class="d-flex">
                    <div class="">
                      <img src="{{ asset('storage/product_thumbs') }}/{{ $product->thumbnail->thumbnail ?? '' }}" alt="" width="80">
                    </div>
                    <div class="ml-3">
                      <a href="@if(Route::has('product')) {{ route('product', $product->slug) }} @endif" target="_blank" class="d-inline-block text-dark mb-1 title">{{ $product->title ?? '' }}</a>
                      <p class="text-muted small"></p>
                    </div>
                  </div>
                </div>
                <!-- end item description -->

                <!-- qty -->
                <div class="col-md-2">
                  <div class="d-flex justify-content-center mb-2 mb-md-0">
                    <input type="number" class="form-control px-3" name="qty[]" value="{{ $carts[$k]['qty'] ?? '' }}" placeholder="1" min="0">
                  </div>
                </div>
                <!-- end qty -->

                <!-- price -->
                <div class="col-md-2">
                  <div class="price-wraper mb-2 text-center text-md-left">
                    <div class="text-bold">&#2547;{{ number_format($product->priceAfterDiscount() * $carts[$k]['qty'], 2) }}</div>
                    <small class="text-muted">&#2547;{{ number_format($product->priceAfterDiscount(), 2) }} each</small>
                  </div>
                </div>
                <!-- end price -->

                <!--  remove -->
                <div class="col-md-2">
                  <div class="d-flex border-bottom-sm justify-content-center justify-content-md-start">
                    <button type="button" class="btn btn-light" name="button" onclick="removeFromCart({{ $product->id ?? '' }})"><i class="far fa-trash-alt pr-1"></i> Remove</button>
                  </div>
                </div>
                <!-- remove -->
              </div>
              <!-- end item row -->
            @endforeach
          @else
            <h6 class="text-center p-5 border">Cart is empty!</h6>
          @endif


          @if(isset($carts) && count($carts) > 0)
          <div class="row mb-3">
            <div class="col-12">
              <hr class="mt-0">
              <div class="d-flex justify-content-end p-3">
                <button type="submit" name="button" class="btn text-bold btn-light px-5 border-secondary">Update Cart</button>
              </div>
            </div>
          </div>
          @endif
        </form>

        @if(auth()->check() && isset($carts) && count($carts) > 0)
          <!-- shipping address -->
          <div class="row border-top">
            <div class="col-12">
              <div class="filter-wraper">
                <h6 class="title py-4">
                  <a class="dropdown-toggle section-cart__head" data-toggle="collapse" href="#collapseExample3" role="button" aria-expanded="false" aria-controls="collapseExample">
                    SHIPPING ADDRESS
                  </a>
                </h6>
                <div class="collapse show" id="collapseExample3">
                  <div class="px-4 pb-4">
                    <form class="" action="index.html" method="post">
                        <div class="form-group">
                          <!-- address for js use -->
                          <script>
                              let addresses = @php echo $addresses_json @endphp;
                          </script>

                          <label for="" class="text-muted">Use this address: </label>
                          <select class="form-control" name="" id="saved_addresses" onchange="saveShippingAddress('change_address')">
                            <option value="new">Add new address</option>
                            @foreach($addresses as $address)
                              <option value="{{ $address->id ?? '' }}" @if(isset($session_address->id) && $session_address->id == $address->id) {{'selected'}} @endif>
                                {{ $address->address_1 ?? ''}} {{$address->state ?? ''}} {{$address->city ?? ''}} {{$address->zip ?? ''}} {{$address->country ?? ''}}
                              </option>
                            @endforeach
                          </select>
                        </div>
                    </form>
                  </div>

                  <div class="px-4 pb-4">
                    <form id="shipping_address_form" class="" action="" method="post">
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <input type="text" class="form-control" id="full_name" value="{{ $session_address->full_name ?? '' }}" placeholder="Full Name" required>
                          </div>
                          <div class="form-group col-md-6">
                            <input type="tel" class="form-control" id="phone_number" value="{{ $session_address->mobile ?? '' }}" placeholder="Mobile Number" required>
                          </div>
                        </div>
                        <div class="form-group">
                          <input type="text" class="form-control" id="address" value="{{ $session_address->address_1 ?? '' }}" placeholder="Building, Street and etc." required>
                        </div>
                        <div class="form-row">
                          <div class="form-group col-md-3">
                            <input type="text" class="form-control" id="division" value="{{ $session_address->state ?? '' }}" placeholder="Division" required>
                          </div>
                          <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="city" value="{{ $session_address->city ?? '' }}" placeholder="City" required>
                          </div>
                          <div class="form-group col-md-2">
                            <input type="text" class="form-control" id="postal_code" value="{{ $session_address->zip ?? '' }}" placeholder="Postal Code" required>
                          </div>
                          <div class="form-group col-md-3">
                            <select class="form-control" id="country" required>
                              <option value="Bangladesh">Bangladesh</option>
                            </select>
                          </div>
                        </div>
                        <div class="d-flex justify-content-end my-2">
                          <button type="button" class="btn text-bold btn-light cart-btn-save border-secondary" onclick="saveShippingAddress('save')">Save Address</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- end shipping address -->

          <!-- shipping address -->
          <div class="row border-top">
            <div class="col-12">
              <div class="filter-wraper">
                <h6 class="title py-4 mb-0">
                  <a class="dropdown-toggle section-cart__head" data-toggle="collapse" href="#collapseExample4" role="button" aria-expanded="false" aria-controls="collapseExample">
                    PAYMENT METHODS
                  </a>
                </h6>
                <div class="collapse show" id="collapseExample4">
                  <div class="">
                    <div class="collapse show" id="collapseExample4">
                      <div class="mb-4 ml-3">
                        <div class="custom-control custom-radio">
                          <input type="radio" id="customRadio1" checked name="customRadio" class="custom-control-input">
                          <label class="custom-control-label" for="customRadio1">SSLCommerz</label>
                          <small class="muted-text">(Visa / Master / AMX / bKash / Rocket & More)</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- end shipping address -->
        @endif


          <div class="row border-top">
            <div class="col-12">
              <div class="d-flex justify-content-between p-3">
                <a href="/" class="btn btn-light"><i class="fas fa-chevron-left"></i> Continue Shoping</a>


                  @if(auth()->check() && isset($carts) && count($carts) > 0)
                  <form class="" id="place_order_btn" action="pay" method="post">
                    <button type="button" class="btn btn-primary-theme text-bold px-5" name="button" onclick="checkLoginStatus()">Place Order <i class="fas fa-chevron-right"></i></button>
                  </form>
                  @else
                    @if(isset($carts) && count($carts) > 0)
                      <button type="button" class="btn btn-primary-theme text-bold px-5" name="button" onclick="checkLoginStatus()">Check Out <i class="fas fa-chevron-right"></i></button>
                    @endif
                  @endif

              </div>
            </div>
          </div>

        </form>

        </div>
      </div>

      <div class="col-md-3">
        <div class="mb-3 bg-white p-4 coupon_wraper border">
          <h6>Have Coupon?</h6>
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Coupon code">
            <div class="input-group-append">
              <button type="submit" class="btn theme-btn-primary text-white">Apply</button>
            </div>
          </div>
        </div>

        <div class="bg-white p-4 border">
          <div class="d-flex justify-content-between">
            <div class="">Total Price:</div>
            <div class="">&#2547;{{ $totals['total_price'] ?? '' }}</div>
          </div>
          <div class="d-flex justify-content-between">
            <div class="">Discount:</div>
            <div class="">&#2547;{{ $totals['discount'] ?? '' }}</div>
          </div>
          <div class="d-flex justify-content-between">
            <div class=""><strong>Total:</strong></div>
            <div class="total-price"><strong>&#2547;{{ $totals['total'] ?? '' }}</strong></div>
          </div>
          <hr class="my-4">
          <div class="text-center">
            <img src="{{ asset('frontend/img/SSLCommerz-logo.webp') }}" alt="" height="100">
          </div>
        </div>
      </div>
    </div>

  </div>
</section><!-- end cart section -->





@if(!auth()->check())
<!-- login & regitration model -->
<div class="modal fade" id="login_registration_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">

        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Sign in</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Register</a>
          </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
          <!-- login tab -->
          <div class="tab-pane fade show active mb-4 pt-2" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

            <!-- error container -->
            <div id="login_error_container"></div>

            <form method="POST" action="{{ route('login') }}" id="user_popup_login">
                @csrf
                <!-- Section login -->
                <div class="d-flex justify-content-center align-items-center mb-5 mt-2">
                  <div class="">
                    <div class="bg-white p-4">
                        <button type="button"class="btn btn-facebook btn-block" name="button" onclick="fb_login()"> <i class="fa fa-facebook"></i> Sign in with Facebook</button>
                        <div class="separator text-muted my-3">OR</div>

                        <div class="form-group">
                          <input id="email" type="email" class="form-control" name="" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                          <input id="password" type="password" class="form-control" name="" placeholder="Password" required>
                        </div>
                        <div class="">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                            <label class="custom-control-label d-flex flex-nowrap justify-content-between" for="customCheck1">
                              <div class="">Remember me</div>
                              <a class="" href="#">Forgot password?</a>
                            </label>
                          </div>
                        </div>

                        <button type="button" class="btn btn-primary-theme btn-block mt-4" name="button" onclick="loginUser()">Login</button>
                    </div>
                  </div>

                </div>
                <!-- End Section login -->


            </form>
          </div>

          <!-- registration tab -->
          <div class="tab-pane fade my-4" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <!-- error container -->
            <div id="registration_error_container"></div>

            <form id="cart_user_registration" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">First Name</label>
                    <div class="col-md-6">
                        <input id="first_name" type="text" class="form-control" name="first_name" value="" required autocomplete="first_name" autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="last_name" class="col-md-4 col-form-label text-md-right">Last Name</label>

                    <div class="col-md-6">
                        <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="" required autocomplete="name" autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                      <button type="button" class="btn btn-primary-theme btn-block mt-4" name="button" onclick="registerUser()">Register</button>
                    </div>
                </div>
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endif

@push('js')
<script>
  (function (window, document) {
      var loader = function () {
          var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
          script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
          tag.parentNode.insertBefore(script, tag);
      };

      window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
  })(window, document);

</script>

<script>
  function myToast(msg, class_name) {
    var x = document.getElementById("snackbar");
    x.innerHTML = msg;
    x.className = "show text-left "+class_name;
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
  }

  @if($errors->any())
      var x = '';
      @foreach ($errors->all() as $error)
          x += '{{$error}}</br>';
      @endforeach
      myToast(x, 'alert alert-danger');
  @endif

  @if(Session::has('success'))
    myToast("{{Session::get('success')}}", 'alert alert-success text-center');
  @endif
  @if(Session::has('fail'))
    myToast("{{Session::get('fail')}}", 'alert alert-danger text-center');
  @endif
</script>
@endpush
