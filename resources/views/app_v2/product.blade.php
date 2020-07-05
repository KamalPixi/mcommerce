@extends('app_v2.app')
@section('content')
  <!--include header-->
  @include('app_v2.includes.header')

<!-- #### Single Product display #### -->
<section class="section-breadcrumb">
    <div class="container-fluid">
    <div class="row">
        <div class="col">
        <div class="d-flex justify-content-between">
            <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active"><a href="#">{{$product->category->name ?? ''}}</a></li>
                @if(!empty($sub_categories[0]))
                  <li class="breadcrumb-item"><a href="#">{{$sub_categories[0] ?? ''}}</a></li>
                @endif
                @if(!empty($sub_categories[1]))
                  <li class="breadcrumb-item"><a href="#">{{$sub_categories[1] ?? ''}}</a></li>
                @endif
            </ol>
            </nav>
        </div>
        </div>
    </div>
    </div>
</section>

<!-- product details -->
<section class="section section-product-details">
    <div class="section-card">
    <div class="container-fluid">
        <div class="row">

        <!-- product slider -->
        <div class="col-md-5">
            <div class="outer">
              <div id="big" class="owl-carousel owl-theme">
                @foreach($product->images as $k => $image)
                  <div class="item">
                      <img src="{{ asset('storage/product_images/'.$image->image) }}" alt="{{ $product->title ?? '' }}" class="w-100">
                  </div>
                @endforeach
              </div>

              <div id="thumbs" class="owl-carousel owl-theme">
                @foreach($product->images as $k => $image)
                  <div class="item">
                      <img src="{{ asset('storage/product_images/'.$image->image) }}" alt="{{ $product->title ?? '' }}" class="w-100">
                  </div>
                @endforeach
              </div>
            </div>
        </div>
        <!-- end product slider -->

        <div class="col-md-7">
            <div class="product-details-wraper mb-4 pb-2">
            <h2 class="title text-bold mt-3">{{ $product->title ?? '' }}</h2>
            <div class="rating-wraper">
                <ul>
                <li class="rating-score font-weight-semi-bold">{{ number_format($product->rating(), 2) ?? 0}}</li>
                  @php $run = 5; @endphp
                  @for($i=0; $i < (int)$product->rating(); $i++)
                    <li> <i class="fa fa-star checked"></i> </li>
                    @php $run--; @endphp
                  @endfor

                  @for($i=0; $i < $run; $i++)
                    <li> <i class="fa fa-star"></i> </li>
                  @endfor
                </ul>
                <span class="text-low-light">|</span>
                <small class="ml-1 font-weight-semi-bold"><span>{{ $product->totalRating() ?? 0 }}</span> Ratings</small>
                <span class="text-low-light">|</span>
                <small class="ml-1 font-weight-semi-bold text-success"><span>{{ $product->totalSold() ?? 0 }}</span> Sold</small>
            </div>
            <div class="price-wraper d-flex align-items-center flex-wrap">
                <!-- print previous price, if it has discount -->
                @if($product->has_discount)
                  <div class="discounted_price">
                    <del>&#2547; {{ number_format($product->sale_price, 2) }}</del>
                  </div>
                @endif

                <!-- price after discount -->
                <div class="price">&#2547;{{ number_format($product->priceAfterDiscount(), 2) }}</div>


                  <!-- print discount %-->
                  @if($product->has_discount && $product->discount_fixed_price > 0)
                    <div class="badge-off badge bg-red text-white">&#2547;{{ number_format($product->discount_fixed_price, 2) }} OFF</div>
                  @endif

                  @if($product->has_discount && $product->discount_percent > 0)
                    <div class="badge-off badge bg-red text-white">{{ $product->discount_percent ?? '' }}% OFF</div>
                  @endif

            </div>
            </div>


            <div class="product-options-wraper">
            <div class="container-fluid px-0">

              <!-- shipping price -->
                <!-- row -->
                <!-- <div class="row mb-3">
                <div class="col-md-3">
                    <div class="text-offwhite">
                    Shipping Fee
                    </div>
                </div>
                <div class="col-md-9">
                    <i class="fas fa-shipping-fast"></i> Free Shipping
                </div>
                </div> -->
                <!-- end row -->

                <!-- attributes -->
                @foreach($product->attributes as $attribute)
                  <div class="row mb-3">
                  <div class="col-md-3">
                      <div class="text-offwhite">
                        {{ $attribute->key ?? '' }}
                      </div>
                  </div>
                  <div class="col-md-9">
                      <div class="">
                      <select class="form-control w-25 w-sm-100 attribute_key" name="" required>
                        @foreach($attribute->values as $value)
                          <option value="{{ $value->id ?? '' }}">{{ $value->value ?? '' }}</option>
                        @endforeach
                      </select>
                      </div>
                  </div>
                  </div>
                @endforeach


                <!-- row -->
                <div class="row mb-4 pt-3">
                <div class="col-md-3">
                    <div class="text-offwhite">
                    Quantity
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="qty-btns-wraper">
                    <div class="btn-group mr-2" role="group" aria-label="Basic example">
                        <button type="button" class="btn border text-bold" onclick="changeQty('-')">-</button>
                        <input type="text" value="1" id="qty_input" class="form-control input-qty rounded-0 text-center">
                        <button type="button" class="btn border text-bold" onclick="changeQty('+')">+</button>
                        <!-- Quantity increment/decrement -->
                        <script>
                          let qty_input = document.querySelector('#qty_input');
                          function changeQty(op) {
                            if (op === '+') {
                                qty_input.value++;
                            }
                            else {
                              qty_input.value--;
                            }
                          }
                        </script>
                    </div>
                    <span class="text-offwhite">{{ $product->stock ?? 0 }} pieces available</span>
                    </div>
                </div>
                </div>
                <!-- end row -->

                <!-- row -->
                <div class="row">
                <div class="col">
                    <div class="">
                    <button class="btn btn-addtocart" type="button" name="button" onclick="addToCart({{ $product->id ?? '' }})"><i class="fas fa-cart-plus mr-2"></i> Add To Cart</button>
                    <button class="btn btn-buy ml-2" type="button" name="button" onclick="addToCart({{ $product->id ?? '' }}, 'buy_now')">Buy Now</button>
                    </div>
                </div>
                </div>
                <!-- end row -->

            </div>
            </div>

        </div>

        </div>
    </div>
    </div>
</section>
<!-- end product details -->


<!-- product details -->
<section class="section section-product-details">
    <div class="section-card section-header px-4">
        <h5 class="m-0">Product Details</h5>
    </div>
    <div class="section-card px-2">
    <div class="container-fluid">
        <div class="row">
        <div class="col">
            <div class="px-2">
              {!! $product->description ?? '' !!}
            </div>
        </div>
        </div>
    </div>
    </div>
</section>
<!-- product details -->


<!-- product ratings -->
<section class="section section-product-rating mb-4">
    <div class="section-card section-header px-4">
        <h5 class="m-0">Ratings & Reviews</h5>
    </div>
    <div class="section-card px-2">
    <div class="container-fluid">

        <!-- rating row -->
        <div class="row rating-row">
        <div class="col-md-3">
            <div class="rating-score">
            <span class="score">{{ number_format($product->rating(), 2) ?? 0}}</span> out of 5
            </div>
            <div class="rating-wraper">
            <ul>
                @php $run = 5; @endphp
                @for($i=0; $i < (int)$product->rating(); $i++)
                  <li> <i class="fa fa-star checked"></i> </li>
                  @php $run--; @endphp
                @endfor

                @for($i=0; $i < $run; $i++)
                  <li> <i class="fa fa-star"></i> </li>
                @endfor
            </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="rating-details">
            <div class="rating-wraper">
                <ul class="m-0">
                <li class="d-flex">
                    <ul class="mr-2">
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    </ul>
                    <div class="progress-wraper mr-2">
                    <div class="progress h-50 rounded-0">
                        @php
                          $rating_5 = 0;
                          $rating_4 = 0;
                          $rating_3 = 0;
                          $rating_2 = 0;
                          $rating_1 = 0;
                          if($product->totalRating()){
                            $rating_5 = ($product->singleRatingOf(5) * 100) / $product->totalRating();
                            $rating_4 = ($product->singleRatingOf(4) * 100) / $product->totalRating();
                            $rating_3 = ($product->singleRatingOf(3) * 100) / $product->totalRating();
                            $rating_2 = ($product->singleRatingOf(2) * 100) / $product->totalRating();
                            $rating_1 = ($product->singleRatingOf(1) * 100) / $product->totalRating();
                          }
                        @endphp
                        <div class="progress-bar bg-warning" role="progressbar"style="width:{{ $rating_5 }}%"
                          aria-valuenow="{{ $rating_5 }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    </div>
                    <div class="font-weight-semi-bold">
                      {{ $product->singleRatingOf(5) ?? 0 }}
                    </div>
                </li>
                <li class="d-flex">
                    <ul class="mr-2">
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    </ul>
                    <div class="progress-wraper mr-2">
                    <div class="progress h-50 rounded-0">
                      <div class="progress-bar bg-warning" role="progressbar"style="width:{{ $rating_4 }}%"
                        aria-valuenow="{{ $rating_4 }}" aria-valuemin="0" aria-valuemax="100">
                      </div>
                    </div>
                    </div>
                    <div class="font-weight-semi-bold">
                      {{ $product->singleRatingOf(4) ?? 0 }}
                    </div>
                </li>
                <li class="d-flex">
                    <ul class="mr-2">
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    </ul>
                    <div class="progress-wraper mr-2">
                    <div class="progress h-50 rounded-0">
                      <div class="progress-bar bg-warning" role="progressbar"style="width:{{ $rating_3 }}%"
                        aria-valuenow="{{ $rating_3 }}" aria-valuemin="0" aria-valuemax="100">
                      </div>
                    </div>
                    </div>
                    <div class="font-weight-semi-bold">
                      {{ $product->singleRatingOf(3) ?? 0 }}
                    </div>
                </li>
                <li class="d-flex">
                    <ul class="mr-2">
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    </ul>
                    <div class="progress-wraper mr-2">
                    <div class="progress h-50 rounded-0">
                      <div class="progress-bar bg-warning" role="progressbar"style="width:{{ $rating_2 }}%"
                        aria-valuenow="{{ $rating_2 }}" aria-valuemin="0" aria-valuemax="100">
                      </div>
                    </div>
                    </div>
                    <div class="font-weight-semi-bold">
                      {{ $product->singleRatingOf(2) ?? 0 }}
                    </div>
                </li>
                <li class="d-flex">
                    <ul class="mr-2">
                    <li> <i class="fa fa-star checked"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    <li> <i class="fa fa-star"></i> </li>
                    </ul>
                    <div class="progress-wraper mr-2">
                    <div class="progress h-50 rounded-0">
                      <div class="progress-bar bg-warning" role="progressbar"style="width:{{ $rating_1 }}%"
                        aria-valuenow="{{ $rating_1 }}" aria-valuemin="0" aria-valuemax="100">
                      </div>
                    </div>
                    </div>
                    <div class="font-weight-semi-bold">
                      {{ $product->singleRatingOf(1) ?? 0 }}
                    </div>
                </li>
                </ul>
            </div>
            </div>
        </div>
        </div>
        <!-- end rating row -->


        <!-- user review -->
        <div class="row mt-4 p-3">
            <div class="col-md-12">

              @foreach($product->reviews as $review)
                <div class="review-block">
                    <div class="row">
                        <div class="col-md-2 border-right review-block-col">
                            <img src="{{ asset('storage/user_images/user.png') }}" class="rounded" width="70">
                            <div class="review-block-name"><a href="#">nktailor</a></div>
                            <div class="review-block-date">{{ $review->created_at->toDateString() ?? '' }}<br/> {{ $review->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="col-md-10">
                        <div class="rating-wraper">
                          <ul class="my-2">
                            @php $run = 5; @endphp
                            @for($i=0; $i < (int)$review->rating; $i++)
                              <li> <i class="fa fa-star checked"></i> </li>
                              @php $run--; @endphp
                            @endfor

                            @for($i=0; $i < $run; $i++)
                              <li> <i class="fa fa-star"></i> </li>
                            @endfor
                          </ul>
                        </div>
                        <div class="review-block-description">{{ $review->review ?? '' }}</div>
                        </div>
                    </div>
                    <hr/>
                </div>
              @endforeach

            </div>
        </div>
        <!-- end user review -->


        <!-- pagination -->
        <div class="row">
        <div class="col">
            <div class="d-flex justify-content-center mt-1">
              <button type="button" name="button" class="btn theme-btn-primary text-white">Load More <i class="fas fa-sync-alt"></i></button>
            </div>
        </div>
        </div>
        <!-- end pagination -->
    </div>
    </div>
</section>
<!-- product ratings -->
<!-- #### End Single Product display #### -->

  <!--include footer-->
  @include('app_v2.includes.footer')
@endsection

@push('js')
  <script src="{{ asset('frontend/v2/js/owl/owl.thumb.js') }}"></script>
@endpush
