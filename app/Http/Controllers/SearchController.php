<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Search;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Shop;
use App\Models\Attribute;
use App\Models\AttributeCategory;
use App\Models\PreorderProduct;
use App\Utility\CategoryUtility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route; // Add this line to import Route facade

class SearchController extends Controller
{
  public function index(Request $request) 
    {
        // dd($request->all()); // Debugging line - you can uncomment this to see request parameters

        $query = $request->keyword;
        $sort_by = $request->sort_by;
        $product_type = $request->product_type ?? 'general_product';
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;
        $attributes = Attribute::all();
        $selected_attribute_values = array();
        $colors = Color::all();
        $is_available = array();
        $selected_color = null;
        $category = null; // Initialize category object
        $brand = null; // Initialize brand object
        $categories = [];


           // START: নতুন দুটি ভ্যারিয়েবল এখানে null বা খালি হিসেবে শুরু করুন
    // এই ভ্যারিয়েবল দুটি সাইডবারের অবস্থা ঠিক রাখার জন্য পাঠানো হবে
    $parentCategory = null;
    $siblingSubcategories = collect();
    // END: নতুন দুটি ভ্যারিয়েবল এখানে null বা খালি হিসেবে শুরু করুন

        $conditions = [];
        
        // --- Determine if specific page types are requested ---
        $is_featured_search = $request->has('featured') && $request->featured == 1;
        $is_best_selling_search = $request->has('best_selling') && $request->best_selling == 1;

        // --- Handle Category from request (slug or ID) ---
        $category_id_from_request = null;
        if ($request->has('category')) {
            $cat = Category::where('slug', $request->category)->first();
            if ($cat) {
                $category_id_from_request = $cat->id;
                $category = $cat; // Pass the actual category object



                  // START: সাইডবার ঠিক রাখার জন্য মূল কোড
            // এখানে আমরা চেক করছি যে বর্তমান ক্যাটাগরিটি একটি সাব-ক্যাটাগরি কিনা (অর্থাৎ এর parent_id আছে কিনা)
            if ($cat->parent_id != 0) {
                // যদি এটি সাব-ক্যাটাগরি হয়, তাহলে এর parent category এবং ওই parent-এর সকল subcategory (siblings) খুঁজে বের করুন
                $parentCategory = Category::with('childrenCategories')->find($cat->parent_id);
                if ($parentCategory) {
                    $siblingSubcategories = $parentCategory->childrenCategories;
                }
            }
            // END: সাইডবার ঠিক রাখার জন্য মূল কোড


            }
        }

        // --- Handle Brand from request (slug or ID) ---
        $brand_id_from_request = null;
        if ($request->has('brand')) {
            $br = Brand::where('slug', $request->brand)->first();
            if ($br) {
                $brand_id_from_request = $br->id;
                $brand = $br; // Pass the actual brand object
            }
        }
        // --- End: Determine page types and get category/brand objects ---


        // --- Preorder Products Logic ---
        if(addon_is_activated('preorder') && $request->product_type == 'preorder_product'){
            $products = PreorderProduct::where('is_published',1);
            $products = filter_preorder_product($products); // Assuming this helper exists and applies base filters
            
            if ($category_id_from_request != null) { 
                $category_ids_for_filter = CategoryUtility::children_ids($category_id_from_request);
                $category_ids_for_filter[] = $category_id_from_request;
                $products = $products->whereIn('category_id', $category_ids_for_filter); 
            }

            if ($min_price != null && $max_price != null) {
                $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
            }

            // Keyword search for preorder products
            if ($query != null) {
                $products->where(function ($q) use ($query) {
                    foreach (explode(' ', trim($query)) as $word) {
                        $q->where('product_name', 'like', '%' . $word . '%')
                            ->orWhere('tags', 'like', '%' . $word . '%')
                            ->orWhereHas('preorder_product_translations', function ($q) use ($word) {
                                $q->where('product_name', 'like', '%' . $word . '%');
                            });
                    }
                });
                $case1 = $query . '%';
                $case2 = '%' . $query . '%';
                $products->orderByRaw('CASE WHEN product_name LIKE "'.$case1.'" THEN 1 WHEN product_name LIKE "'.$case2.'" THEN 2 ELSE 3 END');
            }

            // Preorder specific sorting
            switch ($sort_by) {
                case 'newest': $products->orderBy('created_at', 'desc'); break;
                case 'oldest': $products->orderBy('created_at', 'asc'); break;
                case 'price-asc': $products->orderBy('unit_price', 'asc'); break;
                case 'price-desc': $products->orderBy('unit_price', 'desc'); break;
                default: $products->orderBy('id', 'desc'); break;
            }

            $products = $products->with('taxes')->paginate(12, ['*'], 'preorder_product')->appends(request()->query());
            
            // Return view for preorder products
            return view('frontend.product_listing', compact(
                'products', 'query', 'category', 'categories', 'category_id_from_request', 
                'brand', 'brand_id_from_request', 'sort_by', 'seller_id', 'min_price', 'max_price', 
                'attributes', 'selected_attribute_values', 'colors', 'selected_color',
                'product_type','is_available', 'is_featured_search', 'is_best_selling_search',  'parentCategory', 'siblingSubcategories'
            ));            
        }

        // --- General Products Logic ---
        // Base query based on specific page types (featured, best selling) or general conditions
        if ($is_featured_search) {
            $products = Product::where('featured', 1); 
        } elseif ($is_best_selling_search) { 
            $products = Product::orderBy('num_of_sale', 'desc'); 
        } else {
            $products = Product::where($conditions); 
        }
        $products->where('published', 1); // Ensure products are published


        // Apply Category filter if a category is selected
        if ($category_id_from_request != null) { 
            $category_ids_for_filter = CategoryUtility::children_ids($category_id_from_request);
            $category_ids_for_filter[] = $category_id_from_request;
            $products = $products->whereIn('category_id', $category_ids_for_filter);

            $attribute_ids = AttributeCategory::whereIn('category_id', $category_ids_for_filter)->pluck('attribute_id')->toArray();
            $attributes = Attribute::whereIn('id', $attribute_ids)->get();
        } else {
            $categories = Category::with('childrenCategories', 'coverImage')->where('level', 0)->orderBy('order_level', 'desc')->get();
        }

        // Apply Brand filter if a brand is selected
        if ($brand_id_from_request != null) { 
            $products->where('brand_id', $brand_id_from_request); 
        }

        // Price range filter
        if ($min_price != null && $max_price != null) {
            $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        // Keyword search for general products
        if ($query != null) {
            $this->store($request); // Assuming this stores search queries
            $products->where(function ($q) use ($query) {
                foreach (explode(' ', trim($query)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });
            $case1 = $query . '%';
            $case2 = '%' . $query . '%';
            $products->orderByRaw('CASE WHEN name LIKE "'.$case1.'" THEN 1 WHEN name LIKE "'.$case2.'" THEN 2 ELSE 3 END');
        }

        // Sorting for general products (only if not a best_selling specific query)
        if (!$is_best_selling_search && $sort_by) {
            switch ($sort_by) {
                case 'newest': $products->orderBy('created_at', 'desc'); break;
                case 'oldest': $products->orderBy('created_at', 'asc'); break;
                case 'price-asc': $products->orderBy('unit_price', 'asc'); break;
                case 'price-desc': $products->orderBy('unit_price', 'desc'); break;
                default: $products->orderBy('id', 'desc'); break;
            }
        } elseif (!$is_best_selling_search) {
            $products->orderBy('id', 'desc'); // Default sorting
        }
    
        // Color filtering
        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }

        // Attribute filtering
        if ($request->has('selected_attribute_values')) {
            $selected_attribute_values = $request->selected_attribute_values;
            $products->where(function ($query) use ($selected_attribute_values) {
                foreach ($selected_attribute_values as $value) {
                    $str = '"' . $value . '"';
                    $query->orWhere('choice_options', 'like', '%' . $str . '%');
                }
            });
        }
        
        $products = filter_products($products)->with('taxes')->paginate(24)->appends(request()->query());

        // Return view for general products
        return view('frontend.product_listing', compact(
            'products', 'query', 'category', 'categories', 'category_id_from_request', 
            'brand', 'brand_id_from_request', 'sort_by', 'seller_id', 'min_price', 'max_price', 
            'attributes', 'selected_attribute_values', 'colors', 'selected_color',
            'product_type','is_available', 'is_featured_search', 'is_best_selling_search','parentCategory', 'siblingSubcategories'
        ));
    }

    public function listing(Request $request) { return $this->index($request); }

    public function listingByCategory(Request $request, $category_slug) {
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            $request->merge(['category' => $category_slug]); // Merge category slug into the request
            return $this->index($request);
        }
        abort(404);
    }

    public function listingByBrand(Request $request, $brand_slug) {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            $request->merge(['brand' => $brand_slug]); // Merge brand slug into the request
            return $this->index($request);
        }
        abort(404);
    }

    //Suggestional Search
    public function ajax_search(Request $request)
    {
        $keywords = array();
        $query = $request->search;
        $preorder_products = null;
        $products = Product::where('published', 1)->where('tags', 'like', '%' . $query . '%')->get();
        foreach ($products as $key => $product) {
            foreach (explode(',', $product->tags) as $key => $tag) {
                if (stripos($tag, $query) !== false) {
                    if (sizeof($keywords) > 5) {
                        break;
                    } else {
                        if (!in_array(strtolower($tag), $keywords)) {
                            array_push($keywords, strtolower($tag));
                        }
                    }
                }
            }
        }

        $products_query = filter_products(Product::query());

        $products_query = $products_query->where('published', 1)
            ->where(function ($q) use ($query) {
                foreach (explode(' ', trim($query)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });
        $case1 = $query . '%';
        $case2 = '%' . $query . '%';

        $products_query->orderByRaw('CASE
                WHEN name LIKE "'.$case1.'" THEN 1
                WHEN name LIKE "'.$case2.'" THEN 2
                ELSE 3
                END');
        $products = $products_query->limit(3)->get();

        $categories = Category::where('name', 'like', '%' . $query . '%')->get()->take(3);

        $shops = Shop::whereIn('user_id', verified_sellers_id())->where('name', 'like', '%' . $query . '%')->get()->take(3);

        if(addon_is_activated('preorder')){
            $preorder_products =  PreorderProduct::where('is_published', 1)
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('product_name', 'like', '%' . $query . '%')
                    ->orWhere('tags', 'like', '%' . $query . '%');
            })
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('user_type', 'admin');
                })->orWhereHas('user.shop', function ($q) {
                    $q->where('verification_status', 1);
                });
            })
            ->limit(3)
            ->get();
            
        }

        if (sizeof($keywords) > 0 || sizeof($categories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0  || sizeof($preorder_products) > 0){
            return view('frontend.partials.search_content', compact('products', 'categories', 'keywords', 'shops','preorder_products'));
        }
        return '0';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $search = Search::where('query', $request->keyword)->first();
        if ($search != null) {
            $search->count = $search->count + 1;
            $search->save();
        } else {
            $search = new Search;
            $search->query = $request->keyword;
            $search->save();
        }
    }
}