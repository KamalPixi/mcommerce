<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HelperClasses\Helper;
use App\AppModels\Product;
use App\AppModels\Brand;
use App\AppModels\Category;
use App\AppModels\ShippingMethod;

class SearchController extends Controller {

    public function Search(Request $request) {
      $d = $request->validate([
        'keyword' => 'required|max:255',
        'categories' => 'nullable|max:255',
        'min' => 'nullable|numeric|digits_between:1,10',
        'max' => 'nullable|numeric|digits_between:1,10',
        'sort_by' => 'nullable|max:100',
        'brands' => 'nullable|max:255'
      ]);


      // make categories string input to array
      if (isset($d['categories'])) {
        $d['categories'] = explode(',', $d['categories']);
      }
      if (isset($d['brands'])) {
        $d['brands']= explode(',', $d['brands']);
      }
      // to show keyword based options on filter section.
      $ps = Product::where('title', 'LIKE', "%{$d['keyword']}%")->paginate(10);
      // get category ids
      $category_ids = array();
      foreach ($ps as $product) {
        array_push($category_ids, $product->category_id);
      }
      // get categories
      $categories = Category::find($category_ids);

      // filter product based on based on user inputs
      // get those category_ids that user filtered
      $products_query = Product::query();
      $products_query->where('title', 'LIKE', "%{$d['keyword']}%");
      if (isset($d['min']) && !empty($d['min']) && isset($d['max']) && !empty($d['max'])) {
        $products_query->whereBetween('sale_price', [$d['min'], $d['max']]);
      }
      if (isset($d['sort_by']) && !empty($d['sort_by']) && $d['sort_by'] == 'low') {
        $products_query->orderBy('sale_price', 'asc');
      }
      if (isset($d['sort_by']) && !empty($d['sort_by']) && $d['sort_by'] == 'high') {
        $products_query->orderBy('sale_price', 'desc');
      }

      $cats = '';
      $cat_ids = array();
      if (isset($d['categories']) && is_array($d['categories']) && count($d['categories']) > 0) {
        $cats = Category::WhereIn('name', $d['categories'])->get();
        foreach ($cats as $c) {
          array_push($cat_ids, $c->id);
        }
        $products_query->WhereIn('category_id', $cat_ids);
      }


      // get those brand_ids that user filtered
      $the_brands = array();
      $the_brand_ids = array();
      if (isset($d['brands']) && is_array($d['brands']) && count($d['brands']) > 0) {
        $the_brands = Brand::WhereIn('name', $d['brands'])->get();
        foreach ($the_brand_ids as $b) {
          array_push($the_brand_ids, $b->id);
        }
        $products_query->WhereIn('brand_id', $the_brand_ids);
      }


      $products = $products_query->paginate(20);


      // get brands
      $brand_ids = array();
      foreach ($products as $product) {
        array_push($brand_ids, $product->brand_id);
      }
      $brands = Brand::find($brand_ids);

      // to calculate total items in a single category
      $category_count = array_count_values($category_ids);
      $brand_count = array_count_values($brand_ids);

      return view('app_v2.includes.search_result', compact('products', 'categories', 'category_count', 'brands', 'brand_count', 'd'));
    }


    public function Query(Request $request) {
      $d = $request->validate([
        'query' => 'required|max:255'
      ]);

      $products = Product::Where('title', 'LIKE', "%{$d['query']}%")->get();
      return response()->json([
        'query' => view('app_v2.includes.query', compact('products'))->render(),
      ]);
    }
}
