<section class="section">
    <!-- section product -->
    <!-- section header -->
    <div class="section-card section-header border">
    <div class="container-fluid">
        <div class="row">
        <div class="col">
            <div class="d-flex justify-content-between">
                <div class="text-bold text-primary-theme">
                TOP PRODUCTS
                </div>
                <div class="text-right">
                <a class="text-primary-theme" href="#">See All <span>></span> </a>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
    <!--end section header -->
    <!-- section body -->
    <div class="section-product">
    <div class="container-fluid px-0">
    <div class="row no-gutters">

        @foreach($top_products as $product)
            <!-- item -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2">
            <div class="card mr-2">
                <a href="@if(Route::has('product')) {{ route('product', [$product->slug]) }} @endif">
                    <img class="lozad card-img-top w-100" data-src="{{ asset('storage/product_thumbs')}}/{{ $product->thumbnail->thumbnail ?? '' }}" alt="{{ $product->title ?? 'product img' }}">
                </a>
                <figcaption class="card-body text-center">
                <a class="card-text product-title" href="@if(Route::has('product')) {{ route('product', [$product->slug]) }} @endif"> {{ $product->strLimit() ?? '' }} </a>
                <div class="text-bold product-price">
                    <!-- print previous price, if it has discount -->
                    @if($product->has_discount)
                     <del class="text-muted">&#2547; {{ number_format($product->sale_price, 2) }} </del>
                    @endif
                    <br>

                    <!-- price after discount -->
                    &#2547;
                    @if($product->has_discount)
                        {{ number_format($product->priceAfterDiscount(), 2) }}
                    @else
                        {{ number_format($product->sale_price, 2) }}
                    @endif

                </div>
                </figcaption>
            </div>
            </div>
            <!-- end item -->
        @endforeach

    </div>
    </div>
</div>
</section>
<!-- end section product -->
