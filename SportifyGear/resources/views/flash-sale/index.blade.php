<x-frontend.layout>

    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            <!-- Flash Sale Header with Countdown -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl overflow-hidden mb-8 md:mb-12">
                <div class="px-4 py-6 md:py-8 lg:py-12 md:px-8 lg:px-12 text-center text-white">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-3 md:mb-4">🔥 FLASH SALE
                        🔥</h1>
                    <p class="text-base sm:text-lg md:text-xl text-red-100 mb-4 md:mb-6 px-2">Huge discounts on selected
                        items - hurry, while stocks last!</p>

                </div>
            </div>

            <!-- Filters & Sorting -->
            <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 mb-8">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('flash-sale.index') }}"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-lg {{ !request('category') ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition whitespace-nowrap">
                        All Flash Sale
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('flash-sale.index', ['category' => $category->slug]) }}"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-lg {{ request('category') == $category->slug ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition whitespace-nowrap">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" class="flex gap-2 w-full md:w-auto">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <select name="sort" onchange="this.form.submit()"
                        class="w-full md:w-auto rounded-lg border-gray-300 py-2 px-4 text-sm focus:ring-red-500 focus:border-red-500 bg-white shadow-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="discount_desc" {{ request('sort') == 'discount_desc' ? 'selected' : '' }}>Biggest
                            Discount</option>
                        <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>
                            Price: Low to High</option>
                        <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>
                            Price: High to Low</option>
                    </select>
                </form>
            </div>

            <!-- Products Grid – with the exact card design from "Latest Products" -->
            @if ($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach ($products as $product)
                        @php
                            // Replicate variant logic from the second design (choose first variant if exists)
                            $variant = $product->variants->first();
                            $originalPrice = $product->best_original_price ?? ($variant->price ?? 0);
                            $finalPrice = $product->best_discounted_price ?? ($variant->price ?? 0);
                            $discountLabel = null;

                            if ($product->best_discount_percent > 0) {
                                $discountLabel = '-' . number_format($product->best_discount_percent) . '%';
                            } elseif ($variant && $variant->discounts->isNotEmpty()) {
                                $discount = $variant->discounts->first();
                                if ($discount->discount_type === 'percentage') {
                                    $discountLabel = '-' . $discount->discount_value . '%';
                                } else {
                                    $discountLabel = '-Rs ' . number_format($discount->discount_value);
                                }
                            }

                            // Image: variant primary image > variant first image > product display image
                            $variantImage = $variant
                                ? ($variant->primary_image
                                    ? asset('storage/' . $variant->primary_image)
                                    : ($variant->images->first()
                                        ? asset('storage/' . $variant->images->first()->image_path)
                                        : $product->display_image))
                                : $product->display_image;

                            // Stock & description
                            $stockQuantity = $variant->stock_quantity ?? 0;
                            $variantDescription = $variant->description ?? ($product->variant_description ?? null);
                        @endphp

                        <a href="{{ route('products.show', $product->slug) }}"
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-100 transform hover:-translate-y-0.5 flex flex-col relative">

                            <!-- Discount Badge (top-right, matching second design) -->
                            @if ($discountLabel)
                                <div
                                    class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-[10px] sm:text-xs font-bold px-2.5 py-1 rounded-full shadow-md z-10">
                                    {{ $discountLabel }}
                                </div>
                            @endif

                            <!-- Product Image (object-contain, light background) -->
                            <div
                                class="relative bg-gray-100 h-32 sm:h-44 md:h-52 flex items-center justify-center overflow-hidden p-2">
                                <img src="{{ $variantImage }}"
                                    class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-110"
                                    alt="{{ $product->name }}">
                            </div>

                            <!-- Product Details -->
                            <div class="p-3 sm:p-4">
                                @if ($product->category)
                                    <div
                                        class="text-[10px] sm:text-xs text-orange-500 font-medium mb-1 uppercase tracking-wide">
                                        {{ $product->category->name }}
                                    </div>
                                @endif

                                <h3
                                    class="font-bold text-sm sm:text-base text-gray-800 line-clamp-2 group-hover:text-orange-600 transition">
                                    {{ $product->name }}
                                </h3>

                                @if ($variantDescription)
                                    <p class="text-[11px] sm:text-xs text-gray-500 mt-1.5 line-clamp-2">
                                        {!! Str::limit(strip_tags($variantDescription), 120) !!}
                                    </p>
                                @endif

                                <div class="mt-3 flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-2 flex-wrap">
                                    <span class="text-lg sm:text-xl font-bold text-orange-600">
                                        Rs. {{ number_format($finalPrice, 2) }}
                                    </span>

                                    @if ($finalPrice < $originalPrice)
                                        <span class="text-gray-400 line-through text-xs sm:text-sm">
                                            Rs. {{ number_format($originalPrice, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Rating & Stock (exactly as second design) -->
                                <div class="mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    @if ($product->reviews_count > 0)
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex items-center gap-0.5">
                                                @php $rating = round($product->reviews_avg_rating * 2) / 2; @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($rating >= $i)
                                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-yellow-400 fill-current"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-300"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-gray-400 text-[10px] sm:text-xs">
                                                ({{ $product->reviews_count }})
                                            </span>
                                        </div>
                                    @else
                                        <div></div>
                                    @endif

                                    <!-- Stock -->
                                    @if ($stockQuantity > 0)
                                        <div class="flex items-center gap-1 text-[10px] sm:text-xs text-green-600">
                                            <span>{{ $stockQuantity }} in stock</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 text-[10px] sm:text-xs text-red-500">
                                            <span>Out of Stock</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 md:mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12 sm:py-16 bg-white rounded-2xl shadow-sm">
                    <svg class="w-16 h-16 sm:w-24 sm:h-24 mx-auto text-gray-300 mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-700 mb-2">No flash sale products found</h3>
                    <p class="text-sm sm:text-base text-gray-500 px-4">Check back later for amazing deals!</p>
                    <a href="{{ route('home') }}"
                        class="inline-block mt-4 bg-red-600 text-white px-5 sm:px-6 py-2 rounded-lg hover:bg-red-700 transition text-sm sm:text-base">
                        Continue Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Countdown timer (same as original)
            function getNextFlashSaleEnd() {
                let end = new Date();
                end.setDate(end.getDate() + 2);
                end.setHours(23, 59, 59, 999);
                return end;
            }

            function updateCountdown() {
                const now = new Date().getTime();
                const endTime = localStorage.getItem('flashSaleEnd');
                let target;
                if (endTime) {
                    target = new Date(parseInt(endTime));
                    if (target <= now) {
                        localStorage.setItem('flashSaleEnd', getNextFlashSaleEnd().getTime());
                        target = getNextFlashSaleEnd();
                    }
                } else {
                    target = getNextFlashSaleEnd();
                    localStorage.setItem('flashSaleEnd', target.getTime());
                }

                const diff = target - now;

                if (diff <= 0) {
                    document.getElementById('countdown').innerHTML =
                        '<div class="text-base sm:text-xl font-bold">Sale Ended!</div>';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (86400000)) / (3600000));
                const minutes = Math.floor((diff % 3600000) / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);

                document.getElementById('days').innerText = days.toString().padStart(2, '0');
                document.getElementById('hours').innerText = hours.toString().padStart(2, '0');
                document.getElementById('minutes').innerText = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').innerText = seconds.toString().padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        </script>
    @endpush

</x-frontend.layout>
