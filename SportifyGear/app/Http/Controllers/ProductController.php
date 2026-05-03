<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::query()
            ->with([
                'product' => function ($q) {
                    $q->with([
                        'categories:id,name,slug',
                        'images:id,product_id,image_path,is_primary',
                    ])->withAvg('reviews', 'rating')
                        ->withCount('reviews');
                },
                'discounts' => function ($q) {
                    $q->where(function ($q) {
                        $q->whereNull('start_date')->orWhereDate('start_date', '<=', Carbon::today());
                    })->where(function ($q) {
                        $q->whereNull('end_date')->orWhereDate('end_date', '>=', Carbon::today());
                    });
                },
                'attributeValues.attribute',
                'images',
            ])
            ->whereHas('product', function ($q) {
                $q->where('is_active', true);
            });

        // Search
        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // CATEGORY FILTER
        if ($request->filled('category')) {
            $query->whereHas('product.categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // PRICE RANGE 
        if ($request->filled('min')) {
            $query->where('price', '>=', $request->min);
        }
        if ($request->filled('max')) {
            $query->where('price', '<=', $request->max);
        }

        // ATTRIBUTE FILTERS
        $attributes = Attribute::with('values')->get();
        foreach ($attributes as $attribute) {
            $values = $request->get('attribute_' . $attribute->id, []);
            if (!empty($values)) {
                $query->whereHas('attributeValues', function ($q) use ($values) {
                    $q->whereIn('attribute_value_id', $values);
                });
            }
        }

        // SORTING
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_low_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high_low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy(
                        Product::select('name')
                            ->whereColumn('products.id', 'product_variants.product_id')
                            ->limit(1),
                        'asc'
                    );
                    break;
                case 'name_desc':
                    $query->orderBy(
                        Product::select('name')
                            ->whereColumn('products.id', 'product_variants.product_id')
                            ->limit(1),
                        'desc'
                    );
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $variants = $query->paginate(9)->withQueryString();

        $categories = Category::with('childrenRecursive')->whereNull('parent_id')->get();
        $attributes = Attribute::with('values')->get();

        // Pass variants as 'products' to keep Blade compatibility
        return view('products.index', [
            'products'   => $variants,
            'categories' => $categories,
            'attributes' => $attributes,
        ]);
    }



    public function homeProducts()
    {
        $variants = ProductVariant::query()
            ->with([
                'product' => function ($q) {
                    $q->with([
                        'categories:id,name,slug',
                        'images:id,product_id,image_path,is_primary',
                    ])->withAvg('reviews', 'rating')
                        ->withCount('reviews');
                },
                'discounts' => function ($q) {
                    $q->where(function ($q) {
                        $q->whereNull('start_date')->orWhereDate('start_date', '<=', Carbon::today());
                    })->where(function ($q) {
                        $q->whereNull('end_date')->orWhereDate('end_date', '>=', Carbon::today());
                    });
                },
                'attributeValues.attribute',
                'images',
            ])
            ->whereHas('product', function ($q) {
                $q->where('is_active', true);
            })
            ->latest()
            ->take(16)
            ->get();

        return view('pages.home', compact('variants'));
    }

    public function show($slug)
    {
        $product = Product::with([
            'categories:id,name,slug',
            'images:id,product_id,image_path,is_primary',
            'variants' => function ($q) {
                $q->with([
                    'attributeValues:id,attribute_id,value',
                    'discounts' => function ($dq) {
                        $dq->where(function ($q) {
                            $q->whereNull('start_date')->orWhereDate('start_date', '<=', Carbon::today());
                        })->where(function ($q) {
                            $q->whereNull('end_date')->orWhereDate('end_date', '>=', Carbon::today());
                        });
                    },
                    'images:id,product_variant_id,image_path,is_primary',
                ]);
            },
            'reviews.user:id,name',
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::with([
            'images:id,product_id,image_path,is_primary',
            'variants' => function ($q) {
                $q->with([
                    'attributeValues:id,attribute_id,value',
                    'discounts' => function ($dq) {
                        $dq->where(function ($q) {
                            $q->whereNull('start_date')->orWhereDate('start_date', '<=', Carbon::today());
                        })->where(function ($q) {
                            $q->whereNull('end_date')->orWhereDate('end_date', '>=', Carbon::today());
                        });
                    },
                    'images:id,product_variant_id,image_path,is_primary',
                ]);
            },
        ])
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('products.id', '!=', $product->id)
            ->take(9)
            ->get();

        $cartVariantIds = [];
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cartVariantIds = $cart->items->pluck('product_variant_id')
                    ->map(fn($id) => (string)$id)
                    ->toArray();
            }
        }

        return view('products.show', compact('product', 'relatedProducts', 'cartVariantIds'));
    }


    /**
     * Display flash sale products 
     */
    public function flashSale(Request $request)
    {
        $query = Product::query()
            ->with([
                'categories:id,name,slug',
                'images:id,product_id,image_path,is_primary',
                'variants' => function ($q) {
                    $q->with([
                        'discounts' => function ($dq) {
                            $dq->where(function ($q) {
                                $q->whereNull('start_date')->orWhereDate('start_date', '<=', Carbon::today());
                            })->where(function ($q) {
                                $q->whereNull('end_date')->orWhereDate('end_date', '>=', Carbon::today());
                            });
                        },
                        'attributeValues:id,attribute_id,value',
                        'images:id,product_variant_id,image_path,is_primary',
                    ]);
                },
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true)
            ->whereHas('variants.discounts', function ($q) {
                // Only products with at least one active discount
                $q->where(function ($q) {
                    $q->whereNull('start_date')->orWhereDate('start_date', '<=', Carbon::today());
                })->where(function ($q) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', Carbon::today());
                });
            });

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'discount_desc':
                    break;
                case 'price_low_high':
                    $query->whereHas('variants')->withMin('variants', 'price')->orderBy('variants_min_price', 'asc');
                    break;
                case 'price_high_low':
                    $query->whereHas('variants')->withMin('variants', 'price')->orderBy('variants_min_price', 'desc');
                    break;
                case 'latest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        if ($request->sort === 'discount_desc') {
            $products->getCollection()->transform(function ($product) {
                $product->best_discount_percent = $this->getMaxDiscountPercent($product);
                return $product;
            });
            $products->getCollection()->sortByDesc('best_discount_percent');
        }

        foreach ($products as $product) {
            $best = $this->getBestDiscountedVariant($product);
            $product->best_discount_percent = $best['percent'];
            $product->best_discounted_price = $best['discounted_price'];
            $product->best_original_price = $best['original_price'];
            $product->variant_description = $best['description'];
        }

        $categories = Category::with('childrenRecursive')->whereNull('parent_id')->get();

        return view('flash-sale.index', compact('products', 'categories'));
    }


    private function getMaxDiscountPercent($product)
    {
        $maxPercent = 0;
        foreach ($product->variants as $variant) {
            foreach ($variant->discounts as $discount) {
                $percent = $this->calculateDiscountPercent($variant->price, $discount);
                if ($percent > $maxPercent) {
                    $maxPercent = $percent;
                }
            }
        }
        return $maxPercent;
    }


    private function getBestDiscountedVariant($product)
    {
        $best = [
            'discounted_price' => null,
            'original_price' => null,
            'percent' => 0,
            'description' => null,
        ];
        foreach ($product->variants as $variant) {
            $original = $variant->price;
            $bestDiscountPrice = $original;
            $bestPercent = 0;
            foreach ($variant->discounts as $discount) {
                $finalPrice = $this->applyDiscount($original, $discount);
                $percent = $this->calculateDiscountPercent($original, $discount);
                if ($finalPrice < $bestDiscountPrice) {
                    $bestDiscountPrice = $finalPrice;
                    $bestPercent = $percent;
                }
            }
            if ($bestDiscountPrice < $original && ($best['discounted_price'] === null || $bestDiscountPrice < $best['discounted_price'])) {
                $best['discounted_price'] = $bestDiscountPrice;
                $best['original_price'] = $original;
                $best['percent'] = $bestPercent;
                $best['description'] = $variant->description ?? null;
            }
        }
        return $best;
    }

    private function applyDiscount($price, $discount)
    {
        if ($discount->discount_type === 'percentage') {
            return $price - ($price * $discount->discount_value / 100);
        } else {
            return max(0, $price - $discount->discount_value);
        }
    }

    private function calculateDiscountPercent($originalPrice, $discount)
    {
        if ($discount->discount_type === 'percentage') {
            return $discount->discount_value;
        } else {
            return round(($discount->discount_value / $originalPrice) * 100);
        }
    }
}
