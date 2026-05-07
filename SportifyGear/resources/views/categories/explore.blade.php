<x-frontend.layout>
    <section class="py-12 bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-5">
            <div class="text-center mb-12 animate-fade-in-up">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">
                    Explore Categories
                </h1>
                <p class="text-gray-500 mt-3 max-w-2xl mx-auto text-lg">
                    Browse products by category – discover your next favourite item
                </p>
                <div class="h-1 w-24 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full mx-auto mt-5"></div>
            </div>

            <div class="space-y-20">
                @forelse($categoriesWithProducts as $item)
                    @php
                        $category = $item['category'];
                        $products = $item['products'];
                    @endphp

                    <div class="category-section scroll-mt-20" id="category-{{ $category->id }}">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-orange-100 rounded-xl">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
                                        {{ $category->name }}
                                        @if ($category->childrenRecursive->count())
                                            <span
                                                class="text-sm font-normal text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                                +{{ $category->childrenRecursive->count() }} subcategories
                                            </span>
                                        @endif
                                    </h2>
                                    @if ($category->description)
                                        <p class="text-gray-500 text-sm mt-1 max-w-2xl">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-800 font-semibold bg-orange-50 hover:bg-orange-100 px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow">
                                View all products
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        @if ($products->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                                @foreach ($products as $variant)
                                    @php
                                        $product = $variant->product;
                                        $originalPrice = $variant->price;
                                        $finalPrice = $originalPrice;
                                        $discountLabel = null;

                                        if ($variant->discounts->isNotEmpty()) {
                                            $discount = $variant->discounts->first();
                                            if ($discount->discount_type === 'percentage') {
                                                $finalPrice -= ($originalPrice * $discount->discount_value) / 100;
                                                $discountLabel = '-' . $discount->discount_value . '%';
                                            } else {
                                                $finalPrice -= $discount->discount_value;
                                                $discountLabel = '-Rs ' . number_format($discount->discount_value);
                                            }
                                        }

                                        $variantImage = null;
                                        if ($variant->images->isNotEmpty()) {
                                            $image = $variant->images->sortByDesc('is_primary')->first();
                                            $variantImage = $image ? asset('storage/' . $image->image_path) : null;
                                        }
                                        if (!$variantImage && $product->images->isNotEmpty()) {
                                            $image = $product->images->sortByDesc('is_primary')->first();
                                            $variantImage = $image ? asset('storage/' . $image->image_path) : null;
                                        }
                                        if (!$variantImage) {
                                            $variantImage = asset('images/placeholder.png');
                                        }

                                        $rating = round($product->reviews_avg_rating * 2) / 2;
                                        $inWishlist = in_array($product->id, $wishlistProductIds);
                                    @endphp

                                    <div
                                        class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-200 transform hover:-translate-y-1.5 flex flex-col">
                                        <!-- Discount badge -->
                                        @if ($discountLabel)
                                            <div
                                                class="absolute top-3 right-3 z-20 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md">
                                                {{ $discountLabel }}
                                            </div>
                                        @endif

                                        <div
                                            class="absolute top-3 left-3 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <button type="button"
                                                class="wishlist-btn bg-white/80 backdrop-blur-sm p-1.5 rounded-full shadow-md hover:bg-white transition"
                                                data-product-id="{{ $product->id }}"
                                                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}">
                                                <svg class="w-5 h-5 {{ $inWishlist ? 'text-red-500 fill-current' : 'text-gray-600' }} transition-colors"
                                                    fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <a href="{{ route('products.show', $product->slug) }}?variant={{ $variant->id }}"
                                            class="block">
                                            <div class="relative overflow-hidden bg-gray-100 h-48 sm:h-56 lg:h-64">
                                                <img src="{{ $variantImage }}" loading="lazy"
                                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                    alt="{{ $product->name }}">
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                </div>
                                            </div>

                                            <div class="p-4 flex flex-col flex-grow">
                                                @if ($product->categories->first())
                                                    <div
                                                        class="text-xs text-orange-500 font-semibold mb-1 uppercase tracking-wider">
                                                        {{ $product->categories->first()->name }}
                                                    </div>
                                                @endif

                                                <h3
                                                    class="font-bold text-gray-800 line-clamp-2 group-hover:text-orange-600 transition text-base sm:text-lg">
                                                    {{ $product->name }}
                                                </h3>

                                                @if ($variant->description)
                                                    <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">
                                                        {!! Str::limit(strip_tags($variant->description), 120) !!}
                                                    </p>
                                                @endif

                                                <div class="mt-4 flex items-center justify-between flex-wrap gap-2">
                                                    <div class="flex items-baseline gap-2">
                                                        <span class="text-xl font-bold text-orange-600">
                                                            Rs. {{ number_format($finalPrice, 2) }}
                                                        </span>
                                                        @if ($finalPrice < $originalPrice)
                                                            <span class="text-gray-400 line-through text-sm">
                                                                Rs. {{ number_format($originalPrice, 2) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if ($variant->stock_quantity > 0)
                                                        <div
                                                            class="flex items-center gap-1 text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            <span>{{ $variant->stock_quantity }} in stock</span>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="flex items-center gap-1 text-xs font-medium text-red-500 bg-red-50 px-2 py-0.5 rounded-full">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            <span>Out of stock</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if ($product->reviews_count > 0)
                                                    <div class="mt-3 flex items-center gap-1.5">
                                                        <div class="flex items-center gap-0.5">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($rating >= $i)
                                                                    <svg class="w-4 h-4 text-yellow-400 fill-current"
                                                                        viewBox="0 0 20 20">
                                                                        <path
                                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                                    </svg>
                                                                @elseif ($rating + 0.5 == $i)
                                                                    <svg class="w-4 h-4 text-yellow-400"
                                                                        viewBox="0 0 20 20" fill="currentColor">
                                                                        <defs>
                                                                            <linearGradient
                                                                                id="half-{{ $product->id }}-{{ $i }}">
                                                                                <stop offset="50%"
                                                                                    stop-color="#FBBF24" />
                                                                                <stop offset="50%"
                                                                                    stop-color="#E5E7EB" />
                                                                            </linearGradient>
                                                                        </defs>
                                                                        <path
                                                                            fill="url(#half-{{ $product->id }}-{{ $i }})"
                                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                                    </svg>
                                                                @else
                                                                    <svg class="w-4 h-4 text-gray-300"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path
                                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                                    </svg>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span
                                                            class="text-gray-400 text-xs">({{ $product->reviews_count }})</span>
                                                    </div>
                                                @else
                                                    <div class="mt-3 h-5"></div>
                                                @endif

                                                <div class="mt-4 pt-2 border-t border-gray-100">
                                                    <div
                                                        class="text-center text-orange-500 font-medium text-sm group-hover:text-orange-700 transition flex items-center justify-center gap-1">
                                                        View Details
                                                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-2xl p-12 text-center border border-dashed border-gray-300">
                                <div
                                    class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-medium">No products in this category yet</p>
                                <p class="text-gray-400 text-sm mt-1">Check back later or explore other categories.</p>
                                <a href="{{ route('products.index') }}"
                                    class="inline-block mt-4 text-orange-500 hover:text-orange-700 text-sm font-medium">
                                    Browse all products →
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <p class="text-gray-600 text-lg">No categories found</p>
                        <p class="text-gray-400 mt-2">Please check back later.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <button id="backToTop"
        class="fixed bottom-8 right-8 bg-orange-500 text-white p-3 rounded-full shadow-lg hover:bg-orange-600 transition-all duration-200 opacity-0 invisible z-50"
        aria-label="Back to top">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('backToTop');
            if (backToTopBtn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 400) {
                        backToTopBtn.classList.remove('opacity-0', 'invisible');
                        backToTopBtn.classList.add('opacity-100', 'visible');
                    } else {
                        backToTopBtn.classList.add('opacity-0', 'invisible');
                        backToTopBtn.classList.remove('opacity-100', 'visible');
                    }
                });
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            const wishlistButtons = document.querySelectorAll('.wishlist-btn');
            wishlistButtons.forEach(btn => {
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const productId = this.dataset.productId;
                    const inWishlist = this.dataset.inWishlist === 'true';
                    const svg = this.querySelector('svg');

                    this.disabled = true;

                    try {
                        const response = await fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            const newInWishlist = data.action === 'added';
                            this.dataset.inWishlist = newInWishlist;

                            if (newInWishlist) {
                                svg.classList.add('text-red-500', 'fill-current');
                                svg.classList.remove('text-gray-600');
                                svg.setAttribute('fill', 'currentColor');
                            } else {
                                svg.classList.remove('text-red-500', 'fill-current');
                                svg.classList.add('text-gray-600');
                                svg.setAttribute('fill', 'none');
                            }

                            showToast(data.message, 'success');

                            const wishlistCountElem = document.querySelector('.wishlist-count');
                            if (wishlistCountElem && data.wishlist_count !== undefined) {
                                wishlistCountElem.textContent = data.wishlist_count;
                            }
                        } else {
                            showToast(data.message || 'Something went wrong', 'error');
                        }
                    } catch (error) {
                        console.error('Wishlist error:', error);
                        showToast('An error occurred. Please try again.', 'error');
                    } finally {
                        this.disabled = false;
                    }
                });
            });

            function showToast(message, type = 'success') {
                let toast = document.querySelector('.custom-toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.className =
                        'custom-toast fixed bottom-20 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white text-sm font-medium transition-all duration-300 opacity-0';
                    document.body.appendChild(toast);
                }
                toast.textContent = message;
                toast.classList.remove('bg-green-500', 'bg-red-500');
                toast.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                }, 3000);
            }
        });
    </script>
</x-frontend.layout>
