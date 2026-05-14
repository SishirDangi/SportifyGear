<x-frontend.layout>
    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm text-gray-500">
                <a href="/" class="hover:text-orange-600">Home</a>
                <span class="mx-2">/</span>
                @if ($product->categories->isNotEmpty())
                    <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
                        class="hover:text-orange-600">
                        {{ $product->categories->first()->name }}
                    </a>
                    <span class="mx-2">/</span>
                @endif
                <span class="text-gray-900">{{ $product->name }}</span>
            </nav>

            @php
                function computeVariantPrice($variant, &$discountPercentOut = null)
                {
                    $original = $variant->price ?? 0;
                    $final = $original;
                    $discountPercent = null;
                    if ($variant->discounts->isNotEmpty()) {
                        $discount = $variant->discounts->first();
                        if ($discount->discount_type === 'percentage') {
                            $final -= ($original * $discount->discount_value) / 100;
                            $discountPercent = $discount->discount_value;
                        } else {
                            $final = max(0, $original - $discount->discount_value);
                            if ($original > 0) {
                                $discountPercent = round(($discount->discount_value / $original) * 100);
                            }
                        }
                    }
                    $discountPercentOut = $discountPercent;
                    return ['original' => $original, 'final' => $final];
                }

                $selectedVariantId = request()->query('variant');
                $selectedVariant =
                    $product->variants->firstWhere('id', $selectedVariantId) ?? $product->variants->first();

                $selectedPriceData = computeVariantPrice($selectedVariant, $selectedDiscountPercent);
                $selectedOriginal = $selectedPriceData['original'];
                $selectedFinal = $selectedPriceData['final'];
                $variantsData = [];
                foreach ($product->variants as $variant) {
                    $priceData = computeVariantPrice($variant, $discPercent);
                    $variantsData[$variant->id] = [
                        'id' => $variant->id,
                        'name' => $variant->name ?? 'Variant ' . $variant->id,
                        'original_price' => $priceData['original'],
                        'final_price' => $priceData['final'],
                        'discount_percent' => $discPercent ?? 0,
                        'stock' => $variant->stock_quantity,
                        'description' => $variant->description ?? $product->description,
                        'images' => $variant->images->isNotEmpty()
                            ? $variant->images
                                ->sortByDesc('is_primary')
                                ->map(fn($img) => asset('storage/' . $img->image_path))
                                ->values()
                            : ($product->images->isNotEmpty()
                                ? $product->images
                                    ->sortByDesc('is_primary')
                                    ->map(fn($img) => asset('storage/' . $img->image_path))
                                    ->values()
                                : collect([])),
                    ];
                }
            @endphp

            <!-- Product Detail -->
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

                <!-- IMAGES GALLERY -->
                <div class="lg:w-1/2">
                    @php
                        $currentImages = $variantsData[$selectedVariant->id]['images'] ?? collect();
                        $primaryImage = $currentImages->first() ?? 'https://via.placeholder.com/600x600?text=No+Image';
                    @endphp
                    <div
                        class="w-full h-[500px] bg-gray-100 flex items-center justify-center overflow-hidden rounded-xl">
                        <img id="mainProductImage" src="{{ $primaryImage }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain transition-transform duration-500">
                    </div>
                    <div id="thumbnailContainer"
                        class="grid grid-cols-5 gap-3 mt-4 {{ $currentImages->count() > 1 ? '' : 'hidden' }}">
                        @foreach ($currentImages as $index => $imgUrl)
                            <button onclick="changeMainImage('{{ $imgUrl }}', this)"
                                class="thumbnail-btn relative rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-orange-500' : 'border-transparent' }} hover:border-orange-300 transition-all focus:outline-none">
                                <img src="{{ $imgUrl }}" alt="Thumbnail" class="w-full h-24 object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- PRODUCT DETAILS -->
                <div class="lg:w-1/2">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>

                    @if ($product->categories->isNotEmpty())
                        <div class="mb-3">
                            <span
                                class="inline-block bg-orange-100 text-orange-700 text-sm font-semibold px-3 py-1 rounded-full">
                                {{ $product->categories->pluck('name')->join(', ') }}
                            </span>
                        </div>
                    @endif

                    @if ($product->reviews_count > 0)
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center gap-1">
                                @php $rating = round($product->reviews_avg_rating * 2) / 2; @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($rating >= $i)
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                        </svg>
                                    @elseif($rating + 0.5 == $i)
                                        <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient id="half-grad-{{ $product->id }}">
                                                    <stop offset="50%" stop-color="#FBBF24" />
                                                    <stop offset="50%" stop-color="#E5E7EB" />
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#half-grad-{{ $product->id }})"
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span
                                class="text-gray-600 text-sm font-medium">{{ number_format($product->reviews_avg_rating, 1) }}
                                out of 5</span>
                            <span class="text-gray-400">|</span>
                            <span class="text-gray-600 text-sm">{{ $product->reviews_count }} reviews</span>
                        </div>
                    @endif

                    <!-- PRICE BLOCK -->
                    <div class="mb-6">
                        <div class="flex items-baseline gap-3">
                            <span id="finalPrice" class="text-3xl lg:text-4xl font-bold text-orange-600">Rs.
                                {{ number_format($selectedFinal, 2) }}</span>
                            <span id="originalPrice"
                                class="text-gray-400 line-through text-lg {{ $selectedFinal >= $selectedOriginal ? 'hidden' : '' }}">Rs.
                                {{ number_format($selectedOriginal, 2) }}</span>
                            <span id="discountBadge"
                                class="bg-red-500 text-white text-sm font-semibold px-2 py-1 rounded {{ $selectedDiscountPercent ? '' : 'hidden' }}">
                                {{ $selectedDiscountPercent }}% OFF
                            </span>
                        </div>
                        <p class="text-sm text-green-600 mt-1">Inclusive of all taxes</p>
                    </div>

                    <!-- VARIANT SELECTION -->
                    @if ($product->variants->isNotEmpty())
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Variant:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="variantsContainer">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $priceData = computeVariantPrice($variant, $discPercent);
                                        $vFinal = $priceData['final'];
                                        $vOriginal = $priceData['original'];
                                        $isSelected = $selectedVariant->id == $variant->id;
                                    @endphp
                                    <button type="button"
                                        class="variant-card text-left p-3 rounded-xl border-2 transition-all {{ $isSelected ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-orange-300' }}"
                                        data-variant-id="{{ $variant->id }}">
                                        <div class="font-medium text-gray-900">
                                            {{ $variant->name ?? 'Variant ' . $loop->iteration }}</div>
                                        <div class="flex items-baseline gap-2 mt-1">
                                            <span class="text-orange-600 font-semibold">Rs.
                                                {{ number_format($vFinal, 2) }}</span>
                                            @if ($vFinal < $vOriginal)
                                                <span class="text-gray-400 line-through text-sm">Rs.
                                                    {{ number_format($vOriginal, 2) }}</span>
                                                <span
                                                    class="text-red-500 text-xs font-medium">-{{ $discPercent }}%</span>
                                            @endif
                                        </div>
                                        @if ($variant->attributeValues->isNotEmpty())
                                            <div class="text-xs text-gray-500 mt-1">
                                                @foreach ($variant->attributeValues as $attr)
                                                    {{ $attr->attribute->name }}: {{ $attr->value }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        <div
                                            class="text-xs mt-1 {{ $variant->stock_quantity > 0 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $variant->stock_quantity > 0 ? "In stock ({$variant->stock_quantity})" : 'Out of stock' }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- STOCK STATUS -->
                    <div class="mb-6">
                        <div id="stockInfo" class="flex items-center gap-2">
                            @if ($selectedVariant && $selectedVariant->stock_quantity > 0)
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-green-600 font-medium">{{ $selectedVariant->stock_quantity }} in
                                    stock</span>
                            @else
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-red-500 font-medium">Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    {{-- Varient Descriptions --}}
                    <div class="mb-5">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">
                            Variant Description
                        </h2>

                        <div id="variantDescription">
                            {!! $selectedVariant->description ?? '' !!}
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    @auth
                        <div class="flex gap-3">
                            <button id="addToCartBtn"
                                class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 21v-6">
                                    </path>
                                </svg>
                                Add to Cart
                            </button>
                            <button id="wishlistBtn"
                                class="p-3 border border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3">
                            <a id="buyNowLink" data-base-url="{{ route('orders.place') }}" href="#"
                                class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 text-center">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Buy Now
                            </a>
                        </div>
                    @else
                        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-amber-500 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <div>
                                    <p class="text-amber-700 font-medium">Please login to purchase or book this product</p>
                                    <div class="mt-2 space-x-3">
                                        <a href="{{ route('login') }}"
                                            class="inline-block bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">Login</a>
                                        <a href="{{ route('register') }}"
                                            class="inline-block bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Register</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 opacity-50 cursor-not-allowed">
                            <button disabled
                                class="flex-1 bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg cursor-not-allowed">Login
                                to Add to Cart</button>
                            <button disabled class="p-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3">
                            <button disabled
                                class="w-full bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg cursor-not-allowed">Login
                                to Book</button>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Product Description -->
            <div class="mb-6 mt-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Product Details
                </h1>

                <div id="productDescription" class="text-gray-600 leading-relaxed">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">
                            Product Description
                        </h2>

                        <div id="mainProductDescription">
                            {!! $product->description !!}
                        </div>
                    </div>


                </div>
            </div>

            <!-- Related Products -->
            @if ($relatedProducts->isNotEmpty())
                <div class="mt-16">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">You May Also Like</h2>
                        <a href="{{ route('products.index') }}"
                            class="text-orange-600 hover:text-orange-700 font-medium">View All →</a>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 lg:gap-6">
                        @foreach ($relatedProducts as $rel)
                            @php
                                $relVariant = $rel->variants->first();
                                if ($relVariant) {
                                    $relPriceData = computeVariantPrice($relVariant, $relDiscountPercent);
                                    $relFinal = $relPriceData['final'];
                                    $relOriginal = $relPriceData['original'];
                                    $relImage = null;
                                    if ($relVariant->images->isNotEmpty()) {
                                        $image = $relVariant->images->sortByDesc('is_primary')->first();
                                        $relImage = asset('storage/' . $image->image_path);
                                    }
                                    if (!$relImage && $rel->images->isNotEmpty()) {
                                        $image = $rel->images->sortByDesc('is_primary')->first();
                                        $relImage = asset('storage/' . $image->image_path);
                                    }
                                    if (!$relImage) {
                                        $relImage = 'https://via.placeholder.com/300x300?text=No+Image';
                                    }
                                    $relVariantId = $relVariant->id;
                                } else {
                                    $relFinal = 0;
                                    $relOriginal = 0;
                                    $relImage = 'https://via.placeholder.com/300x300?text=No+Image';
                                    $relDiscountPercent = 0;
                                    $relVariantId = null;
                                }
                            @endphp
                            <a href="{{ route('products.show', $rel->slug) }}?variant={{ $relVariantId }}"
                                class="group bg-white rounded-xl hover:shadow-xl transition-all duration-300 overflow-hidden">
                                <div class="relative overflow-hidden bg-gray-100 aspect-square">
                                    <img src="{{ $relImage }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="{{ $rel->name }}">
                                    @if ($relFinal < $relOriginal)
                                        <span
                                            class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">-{{ $relDiscountPercent }}%</span>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="font-semibold text-gray-800 line-clamp-2 text-sm lg:text-base">
                                        {{ $rel->name }}</h3>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <span class="text-orange-600 font-bold text-sm lg:text-base">Rs.
                                            {{ number_format($relFinal, 2) }}</span>
                                        @if ($relFinal < $relOriginal)
                                            <span class="text-gray-400 line-through text-xs">Rs.
                                                {{ number_format($relOriginal, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    <script>
        function changeMainImage(imageUrl, element) {
            document.getElementById('mainProductImage').src = imageUrl;
            document.querySelectorAll('.thumbnail-btn').forEach(btn => {
                btn.classList.remove('border-orange-500');
                btn.classList.add('border-transparent');
            });
            if (element) {
                element.classList.remove('border-transparent');
                element.classList.add('border-orange-500');
            }
        }

        const variantsData = @json($variantsData);
        let currentSelectedVariantId = {{ $selectedVariant->id }};
        const cartVariantIds = new Set(@json($cartVariantIds));

        // Helper: update URL query parameter without reload
        function updateUrlVariant(variantId) {
            const url = new URL(window.location.href);
            url.searchParams.set('variant', variantId);
            window.history.pushState({}, '', url);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const buyNowLink = document.getElementById('buyNowLink');
            const productId = {{ $product->id }};
            const baseUrl = buyNowLink ? buyNowLink.dataset.baseUrl : '';

            // Helper to update Buy Now link
            function updateBuyNowLink(variantId) {
                if (buyNowLink) {
                    buyNowLink.href = baseUrl + '?productId=' + productId + '&variantId=' + variantId;
                }
            }

            // Set initial link
            updateBuyNowLink(currentSelectedVariantId);

            const variantCards = document.querySelectorAll('.variant-card');
            variantCards.forEach(card => {
                card.addEventListener('click', function() {
                    const variantId = parseInt(this.dataset.variantId);
                    if (variantId === currentSelectedVariantId) return;

                    // Update URL without reload
                    updateUrlVariant(variantId);

                    variantCards.forEach(c => c.classList.remove('border-orange-500',
                        'bg-orange-50'));
                    this.classList.add('border-orange-500', 'bg-orange-50');

                    currentSelectedVariantId = variantId;

                    const data = variantsData[variantId];
                    if (data) {
                        // Update price
                        const finalPriceSpan = document.getElementById('finalPrice');
                        const originalPriceSpan = document.getElementById('originalPrice');
                        const discountBadge = document.getElementById('discountBadge');
                        finalPriceSpan.textContent = 'Rs. ' + data.final_price.toLocaleString(
                            'en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        if (data.final_price < data.original_price) {
                            originalPriceSpan.textContent = 'Rs. ' + data.original_price
                                .toLocaleString('en-IN', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            originalPriceSpan.classList.remove('hidden');
                            if (data.discount_percent > 0) {
                                discountBadge.textContent = data.discount_percent + '% OFF';
                                discountBadge.classList.remove('hidden');
                            } else {
                                discountBadge.classList.add('hidden');
                            }
                        } else {
                            originalPriceSpan.classList.add('hidden');
                            discountBadge.classList.add('hidden');
                        }

                        // Stock
                        const stockInfoDiv = document.getElementById('stockInfo');
                        if (data.stock > 0) {
                            stockInfoDiv.innerHTML =
                                `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="text-green-600 font-medium">${data.stock} in stock</span>`;
                        } else {
                            stockInfoDiv.innerHTML =
                                `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><span class="text-red-500 font-medium">Out of Stock</span>`;
                        }

                        // Description
                        const variantDescDiv = document.getElementById('variantDescription');

                        if (variantDescDiv) {
                            variantDescDiv.innerHTML = data.description || '';
                        }

                        // Images
                        const mainImg = document.getElementById('mainProductImage');
                        const thumbContainer = document.getElementById('thumbnailContainer');
                        if (data.images && data.images.length > 0) {
                            mainImg.src = data.images[0];
                            if (data.images.length > 1) {
                                thumbContainer.innerHTML = '';
                                thumbContainer.classList.remove('hidden');
                                data.images.forEach((imgUrl, idx) => {
                                    const btn = document.createElement('button');
                                    btn.onclick = () => changeMainImage(imgUrl, btn);
                                    btn.className =
                                        `thumbnail-btn relative rounded-lg overflow-hidden border-2 ${idx === 0 ? 'border-orange-500' : 'border-transparent'} hover:border-orange-300 transition-all focus:outline-none`;
                                    btn.innerHTML =
                                        `<img src="${imgUrl}" alt="Thumbnail" class="w-full h-24 object-cover">`;
                                    thumbContainer.appendChild(btn);
                                });
                            } else {
                                thumbContainer.innerHTML = '';
                                thumbContainer.classList.add('hidden');
                            }
                        } else {
                            mainImg.src = 'https://via.placeholder.com/600x600?text=No+Image';
                            thumbContainer.classList.add('hidden');
                        }

                        // Update Buy Now link
                        updateBuyNowLink(variantId);
                    }
                });
            });

            // Add to Cart logic
            @auth
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    if (!currentSelectedVariantId) {
                        showNotification('error', 'Product variant not available');
                        return;
                    }
                    if (cartVariantIds.has(currentSelectedVariantId.toString())) {
                        showNotification('info', 'This item is already in your cart!');
                        return;
                    }
                    fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                variant_id: currentSelectedVariantId,
                                quantity: 1
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', data.message);
                                updateCartCount(data.cart_count);
                                cartVariantIds.add(currentSelectedVariantId.toString());
                            } else {
                                showNotification('error', data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('error', 'Failed to add to cart');
                        });
                });
            }

            // Wishlist
            const wishlistBtn = document.getElementById('wishlistBtn');
            if (wishlistBtn) {
                wishlistBtn.addEventListener('click', function() {
                    fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: '{{ $product->id }}'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', data.message);
                                const icon = wishlistBtn.querySelector('svg');
                                if (data.action === 'added') {
                                    icon.classList.add('text-red-500');
                                    icon.classList.remove('text-gray-500');
                                } else {
                                    icon.classList.remove('text-red-500');
                                    icon.classList.add('text-gray-500');
                                }
                                updateWishlistCount(data.wishlist_count);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
                // Check initial wishlist status
                fetch('{{ route('wishlist.check', $product->id) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.in_wishlist) {
                            const icon = wishlistBtn.querySelector('svg');
                            icon.classList.add('text-red-500');
                            icon.classList.remove('text-gray-500');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        @endauth
        });

        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className =
                `fixed top-20 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : type === 'info' ? 'bg-blue-500' : 'bg-red-500'} transition-opacity duration-300`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function updateCartCount(count) {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = count;
                if (count > 0) el.classList.remove('hidden');
                else el.classList.add('hidden');
            });
        }

        function updateWishlistCount(count) {
            document.querySelectorAll('.wishlist-count').forEach(el => el.textContent = count);
        }
    </script>
</x-frontend.layout>
