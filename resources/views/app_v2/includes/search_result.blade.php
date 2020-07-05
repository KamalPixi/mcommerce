@extends('app_v2.app')
@section('content')
  <!--include header-->
  @include('app_v2.includes.header')
  <!-- #####Content here#### -->

    <!-- section product -->
    <section class="section">
      <!-- section header -->
      <div class="section-card section-header border my-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col">
              <div class="d-flex justify-content-between">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Search result for '{{ $d['keyword'] ?? '' }}'</a></li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--end section header -->

      <!-- section body -->
      <div class="section-product product-listing">
        <div class="container-fluid px-0">
        <div class="row">

          <aside class="col-md-2 overflow-hidden">
            <!-- Price -->
            <div class="filter-wraper border-bottom">
              <h6 class="title mt-0">
                <a class="dropdown-toggle" data-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="false" aria-controls="collapseExample">
                  Price
                </a>
              </h6>
              <div class="collapse show" id="collapseExample2">
                <div class="form-row">
                  <div class="col-md-6">
                    <label for="">Min</label>
                    <input class="form-control filter_price_min" data-name="min" type="number" name="" placeholder="0" min="0" value="{{ $d['min'] ?? '' }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="text-right d-block" for="">Max</label>
                    <input class="form-control filter_price_max" data-name="max" type="number" name="" placeholder="0" min="1" value="{{ $d['max'] ?? '' }}">
                  </div>
                  <button class="btn btn-block btn-primary-theme mx-1 filter_input" type="button" name="button">Apply</button>
                </div>
              </div>
            </div>
            <!-- end price -->

            <!-- categories -->
            <div class="filter-wraper border-bottom">
              <h6 class="title">
                <a class="dropdown-toggle" data-toggle="collapse" href="#collapseExample3" role="button" aria-expanded="false" aria-controls="collapseExample">
                  Categories
                </a>
              </h6>
              <div class="collapse show" id="collapseExample3">
                <div class="">
                  @foreach($categories as $category)
                  <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" @if(isset($d['categories']) && in_array($category->name, $d['categories'])) checked @endif class="custom-control-input filter_input filter_category" id="customCheck_{{ $category->id ?? '' }}" data-name="category" value="{{ $category->name ?? '' }}">
                    <label class="custom-control-label d-flex flex-nowrap justify-content-between" for="customCheck_{{ $category->id ?? '' }}">
                      <div class="">{{ $category->name ?? '' }}</div>
                      <b class="badge badge-pill badge-secondary align-self-center">{{ $category_count[$category->id] ?? '' }}</b>
                    </label>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            <!-- end categories -->

            <!-- categories -->
            <div class="filter-wraper border-bottom">
              <h6 class="title">
                <a class="dropdown-toggle" data-toggle="collapse" href="#collapseExample4" role="button" aria-expanded="false" aria-controls="collapseExample">
                  Brands
                </a>
              </h6>
              <div class="collapse show" id="collapseExample4">
                <div class="">
                  @foreach($brands as $brand)
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" @if(isset($d['brands']) && in_array($brand->name, $d['brands'])) checked @endif class="custom-control-input filter_input filter_brand" id="customCheck_{{ $brand->id ?? '' }}" data-name="brand" value="{{ $brand->name ?? '' }}">
                    <label class="custom-control-label d-flex flex-nowrap justify-content-between" for="customCheck_{{ $brand->id ?? '' }}">
                      <div class="">{{ $brand->name ?? '' }}</div>
                      <b class="badge badge-pill badge-secondary align-self-center">{{ $brand_count[$brand->id] ?? '' }}</b>
                    </label>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            <!-- end categories -->
          </aside>


          <!-- filter top & product listing -->
          <div class="col-md-10">
            <div class="container-fluid px-0">
              <!-- filter top row-->
              <div class="row mb-4">
                  <div class="col-md-9">
                    <div class="text-bold text-dark h-100 d-flex align-items-center">
                      {{ $products->count() ?? '' }} Items found
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="d-flex align-items-center justify-content-lg-end">
                      <div class="">
                        <p class="d-inline-block my-0 mr-2">Sort by</p>
                      </div>
                      <div class="">
                        <select class="form-control d-inline-block filter_sort_by" onchange="filterURL(this)" id="exampleFormControlSelect1" data-name="sort_by">
                          <option value="relevance">Relevance</option>
                          <option value="low" @if(isset($d['sort_by']) && $d['sort_by'] == 'low') selected @endif>Price: low to high</option>
                          <option value="high" @if(isset($d['sort_by']) && $d['sort_by'] == 'high') selected @endif>Price: high to low</option>
                        </select>
                      </div>
                    </div>
                  </div>
              </div>
              <!-- end filter top row-->

              <!-- listing product -->
              <div class="row no-gutters">




                @foreach($products as $product)
                <!-- item -->
                <div class="col-lg-3 col-md-4 col-6 mb-2">
                  <div class="card mr-2">
                      <a href="@if(Route::has('product')) {{ route('product', [$product->slug]) }} @endif">
                          <img class="lozad card-img-top w-100" data-src="{{asset('storage/product_thumbs')}}/{{ $product->thumbnail->thumbnail ?? '' }}" alt="{{ $product->title ?? 'product img' }}">
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
              <!-- end listing product -->

            </div>
          </div>
          <!-- end filter top & product listing -->

        </div>
      </div>
    </div>
    </section>
    <div class="d-flex justify-content-end px-5">
      {{ $products->links() ?? '' }}
    </div>


  <!-- #####Content here#### -->
  <!-- insert search keyword in search field -->
  <script>
    document.getElementsByClassName('input-search')[0].value = '{{ $d['keyword'] ?? '' }}';
  </script>
  <!--include footer-->
  @include('app_v2.includes.footer')
@endsection
