@php
  $cats = App\AppModels\Category::where('category_id', null)->where('is_active', 1)->get();
  $website_setting = App\AppModels\WebsiteSetting::first();

  $carts = session()->get('cart') ? session()->get('cart') : array();
  // get product ids from cart session
  $product_ids = array();
  foreach ($carts as $cart) {
    array_push($product_ids, $cart['product_id']);
  }
  $products = App\AppModels\Product::find($product_ids);
@endphp
<header class="section">
  <section class="bg-white header-main border-bottom">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-lg-2 col-md-3 text-center">
          <a href="/" class="brand-wraper">
            <img class="logo" src="{{ asset('storage/media') }}/{{ $website_setting->logo ?? '' }}" alt="{{ $website_setting->name ?? '' }} logo">
          </a>
        </div>

        <div class="col-lg-7 col-md-5 py-4 py-md-0 position-relative">
          <form class="form-inline">
            <div class="w-100">
              <div class="input-group">
                <input type="text" class="form-control input-search" id="inlineFormInputGroup" placeholder="Search" onkeyup="query(this.value)" onclick="query()" autocomplete="off">
                <div class="input-group-prepend search-btn-wraper">
                  <div class="input-group-text px-4 btn-search"><i class="fas fa-search"></i></div>
                </div>
              </div>
            </div>
          </form>
          <!-- query result prints here -->
          <div id="search_result_box" class="query_result_container position-absolute"></div>
        </div>



        <div class="col-lg-3 col-md-4">
          <div class="widget_wraper cart_wraper d-flex justify-content-center">
            <div class="widget position-relative mr-4">
              <a href="#" class="text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="text-center position-relative">
                  <i class="fas fa-shopping-cart"></i>
                  <span id="cart_size_notify" class="notify">{{ count($carts) }}</span>
                </div>
                <small class="text">Cart</small>
              </a>
              <div id="cart_list_container" class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-item cart-title">
                  <h6 class="mb-0">Cart Items</h6>
                </div>
                <div class="dropdown-item" style="width: 20rem"></div>
                @if(count($carts) > 0)
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
                  <div class="dropdown-item text-center">
                    <a href="/cart" class="btn btn-primary-theme px-5" type="button">Cart <i class="fas fa-shopping-cart" style="font-size: 1rem;"></i></a>
                  </div>
                @else
                  <p class="text-muted ml-3 pl-1">Cart is Empty.</p>
                @endif

              </div>
            </div>

            <div class="widget mr-4">
              <a href="#" class="text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="text-center position-relative">
                  <i class="fas fa-bell"></i>
                  <span class="notify">2</span>
                </div>
                <small class="text">Notification</small>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <button class="dropdown-item" type="button">Notification</button>
                <button class="dropdown-item" type="button">Another action</button>
                <button class="dropdown-item" type="button">Something else here</button>
              </div>
            </div>
            <div class="widget">
              <a href="#" class="text-dark">
                <div class="text-center position-relative">
                  <i class="fas fa-user"></i>
                </div>
                <small class="text">Profile</small>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- navbar -->
  <section class="navbar-wraper bg-white border-bottom">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <nav class="navbar navbar-expand-md navbar-light" id="main_navbar">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown categories-wraper">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars mr-1"></i> Categories
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                          @foreach($cats as $cat)
                              <li class="nav-item dropdown">
                                  <a class="dropdown-item @if(count($cat->categories) > 0) dropdown-toggle @endif" href="#" id="navbarDropdown1" role="button" data-toggle="dropdown"
                                      aria-haspopup="true" aria-expanded="false">
                                      {{ $cat->name ?? '' }}
                                  </a>

                                  @if(count($cat->categories) > 0)
                                  <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                                    @foreach($cat->categories as $child_a)
                                      <li class="nav-item dropdown">
                                          <a class="dropdown-item @if(count($child_a->childrenCategories) > 0) dropdown-toggle @endif" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown"
                                              aria-haspopup="true" aria-expanded="false">
                                              {{ $child_a->name ?? '' }}
                                          </a>
                                          @if(count($child_a->childrenCategories) > 0)
                                          <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                                            @foreach($child_a->childrenCategories as $child_b)
                                              <li><a class="dropdown-item" href="#">{{ $child_b->name ?? '' }}</a></li>
                                            @endforeach
                                          </ul>
                                          @endif
                                      </li>
                                    @endforeach
                                  </ul>
                                  @endif

                              </li>
                          @endforeach
                        </ul>

                    </li>

                  <li class="nav-item">
                    <a class="nav-link" href="#">Services</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Sell with us</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Gift Card</a>
                  </li>
                  <li class="nav-item dropdown categories-wraper">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Pages
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <a class="dropdown-item" href="listing.html">Listing Page</a>
                      <a class="dropdown-item" href="details.html">Details Page</a>
                      <a class="dropdown-item" href="cart.html">Cart Page</a>
                    </div>
                  </li>

                </ul>
                <ul class="navbar-nav">
                  <li class="nav-item">
                    <a class="nav-link" href="#">Get the app</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Sell with us</a>
                  </li>
                </ul>
            </div>
          </nav>

        </div>
      </div>
    </div>
  </section>
</header>








  @push('js')
  <script>
      $(function () {
          $('#main_navbar').bootnavbar({
              //option
              //animation: false
          });
      });
  </script>
  @endpush
