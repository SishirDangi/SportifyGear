<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>

            @if ($wishlistItems->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-2">Your wishlist is empty</h2>
                    <p class="text-gray-500 mb-6">Save your favorite items here to buy them later.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                        Start Shopping
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($wishlistItems as $item)
                        @php
                            $product = $item->product;
                            $variant = $product->variants->first(); // uses first variant (can be improved)
                            $originalPrice = $variant->price ?? 0;
                            $finalPrice = $originalPrice;
                            $discountLabel = null;

                            if ($variant && $variant->discounts->isNotEmpty()) {
                                $discount = $variant->discounts->first();
                                if ($discount->discount_type === 'percentage') {
                                    $finalPrice -= ($originalPrice * $discount->discount_value) / 100;
                                    $discountLabel = $discount->discount_value . '%';
                                } else {
                                    $finalPrice = max(0, $originalPrice - $discount->discount_value);
                                    $discountLabel = '-Rs ' . number_format($discount->discount_value);
                                }
                            }

                            // Primary image logic (same as products index)
                            $variantImage = $variant
                                ? $variant->primary_image ??
                                    ($variant->images->first()
                                        ? asset('storage/' . $variant->images->first()->image_path)
                                        : $product->display_image)
                                : $product->display_image;
                        @endphp

                        <!-- CARD – exact same structure as products.index -->
                        <div class="wishlist-item group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-100 flex flex-col relative"
                            data-product-id="{{ $product->id }}">

                            <!-- DISCOUNT BADGE (absolute) -->
                            @if ($discountLabel)
                                <div
                                    class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md z-10">
                                    @if (str_contains($discountLabel, '%'))
                                        -{{ $discountLabel }}
                                    @else
                                        {{ $discountLabel }}
                                    @endif
                                </div>
                            @endif

                            <!-- Image Container (link to product) -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="relative overflow-hidden bg-gray-100 h-40 sm:h-56">
                                    <img src="{{ $variantImage }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        alt="{{ $product->name }}">
                                </div>
                            </a>

                            <div class="p-3 sm:p-4 flex flex-col flex-grow">
                                <!-- Category Tag (optional) -->
                                @if ($product->categories->isNotEmpty())
                                    <div class="text-xs text-orange-500 font-medium mb-1 uppercase tracking-wide">
                                        {{ $product->categories->first()->name }}
                                    </div>
                                @endif

                                <!-- Product Name (link) -->
                                <a href="{{ route('products.show', $product->slug) }}" class="block">
                                    <h3
                                        class="font-bold text-gray-800 line-clamp-2 group-hover:text-orange-600 transition">
                                        {{ $product->name }}
                                    </h3>
                                </a>

                                <!-- Variant Description (if exists) -->
                                @if ($variant && $variant->description)
                                    <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">
                                        {!! Str::limit(strip_tags($variant->description), 200) !!}
                                    </p>
                                @endif

                                <!-- Price Row -->
                                @if ($variant)
                                    <div class="mt-3 flex items-baseline gap-2 flex-wrap">
                                        <span class="text-xl font-bold text-orange-600">
                                            Rs. {{ number_format($finalPrice, 2) }}
                                        </span>
                                        @if ($finalPrice < $originalPrice)
                                            <span class="text-gray-400 line-through text-sm">
                                                Rs. {{ number_format($originalPrice, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Rating & Stock Row -->
                                <div class="mt-3 flex items-center justify-between">
                                    @if ($product->reviews_count > 0)
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex items-center gap-0.5">
                                                @php $rating = round($product->reviews_avg_rating * 2) / 2; @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($rating >= $i)
                                                        <svg class="w-3.5 h-3.5 text-yellow-400 fill-current"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                        </svg>
                                                    @elseif ($rating + 0.5 == $i)
                                                        <svg class="w-3.5 h-3.5 text-yellow-400" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <defs>
                                                                <linearGradient
                                                                    id="half-grad-wish-{{ $product->id }}-{{ $i }}">
                                                                    <stop offset="50%" stop-color="#FBBF24" />
                                                                    <stop offset="50%" stop-color="#E5E7EB" />
                                                                </linearGradient>
                                                            </defs>
                                                            <path
                                                                fill="url(#half-grad-wish-{{ $product->id }}-{{ $i }})"
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-3.5 h-3.5 text-gray-300 fill-current"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-gray-400 text-xs">({{ $product->reviews_count }})</span>
                                        </div>
                                    @else
                                        <div></div>
                                    @endif

                                    <!-- Stock Status -->
                                    @if ($variant && $variant->stock_quantity > 0)
                                        <div class="flex items-center gap-1 text-xs text-green-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>{{ $variant->stock_quantity }} in stock</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 text-xs text-red-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span>Out of Stock</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons (Add to Cart & Remove) -->
                                <div class="mt-4 flex gap-2">
                                    <button
                                        class="add-to-cart flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition text-sm"
                                        data-variant-id="{{ $variant->id ?? '' }}">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 21v-6">
                                            </path>
                                        </svg>
                                        Add to Cart
                                    </button>
                                    <button
                                        class="remove-from-wishlist p-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50 transition"
                                        data-product-id="{{ $product->id }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        // Add to Cart from Wishlist
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                const variantId = this.dataset.variantId;
                if (!variantId) {
                    alert('Product variant not available');
                    return;
                }

                fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            variant_id: variantId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            if (window.updateCartCountExternal) window.updateCartCountExternal(data
                                .cart_count);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Remove from Wishlist
        document.querySelectorAll('.remove-from-wishlist').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                if (confirm('Remove this item from wishlist?')) {
                    fetch(`/wishlist/remove/${productId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Failed to remove item');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
</x-frontend.layout>
