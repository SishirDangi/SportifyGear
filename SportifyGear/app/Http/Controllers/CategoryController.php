<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    public function navbarCategories()
    {
        return Category::with('children:id,parent_id,name,slug')
            ->whereNull('parent_id')
            ->get();
    }

    public function explore(Request $request)
    {
        $rootCategories = Category::with('childrenRecursive')->whereNull('parent_id')->get();
        $categoriesWithProducts = [];

        $wishlistProductIds = [];
        if (Auth::check()) {
            $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        foreach ($rootCategories as $category) {
            $categoryIds = $this->getCategoryIdsRecursive($category);
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
                ->whereHas('product', function ($q) use ($categoryIds) {
                    $q->where('is_active', true)
                        ->whereHas('categories', function ($subQ) use ($categoryIds) {
                            $subQ->whereIn('categories.id', $categoryIds);
                        });
                })
                ->latest()
                ->limit(8)
                ->get();

            $categoriesWithProducts[] = [
                'category' => $category,
                'products' => $variants,
            ];
        }

        return view('categories.explore', compact('categoriesWithProducts', 'wishlistProductIds'));
    }


    /**
     * Recursively collect all IDs from a category and its children.
     *
     * @param \App\Models\Category $category
     * @return array
     */
    private function getCategoryIdsRecursive(Category $category)
    {
        $ids = [$category->id];
        foreach ($category->childrenRecursive as $child) {
            $ids = array_merge($ids, $this->getCategoryIdsRecursive($child));
        }
        return $ids;
    }
}
