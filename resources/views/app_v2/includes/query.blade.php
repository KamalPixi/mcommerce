@if(count($products) > 0)
  <ul>
    @foreach($products as $product)
      <li><a href="/search?keyword={{ $product->title }}" class="d-block">{{ $product->title ?? '' }}</a></li>
    @endforeach
  </ul>
@else
<ul>
  <li> No match found! </li>
</ul>
@endif
