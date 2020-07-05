<section class="section category-section">
    <!-- section Categrois -->
    <!-- section header -->
    <div class="section-card section-header border-top border-right border-left">
    <div class="container-fluid">
        <div class="row">
        <div class="col">
            <div class="d-flex justify-content-between">
                <div class="text-bold text-primary-theme">
                CATEGORIES
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
    <div class="section-card border pt-0 pb-0">
    <div class="container-fluid">
        <div class="row">
        <div class="col px-0">
            <div class="w-100">
            <div class="category-section__owl owl-carousel">

                @for($i = 0; $i < count($categories); $i=$i+2)
                <!-- item -->
                <div class="owl-item-wraper">
                <a class="category-link d-inline-block pt-2 pb-4 border-bottom border-right" href="#">
                    <div class="owl-single-item d-flex flex-column align-items-center">
                    <img src="{{asset('storage/media/cat-2.jpg')}}" class="d-block w-70 mb-2" alt="...">
                    <div class="category-title">
                        {{ $categories[$i]->name ?? '' }}
                    </div>
                    </div>
                </a>
                <a class="category-link d-inline-block pt-4 pb-4 border-right" href="#">
                    <div class="owl-single-item d-flex flex-column align-items-center">
                    <img src="{{asset('storage/media/cat-2.jpg')}}" class="d-block w-70 mb-2" alt="...">
                    <div class="category-title">
                        {{ $categories[$i+1]->name ?? '' }}
                    </div>
                    </div>
                </a>
                </div>
                <!-- end item -->
                @endfor

            </div>
        </div>
        </div>
    </div>
    </div>
</div>
</section>
