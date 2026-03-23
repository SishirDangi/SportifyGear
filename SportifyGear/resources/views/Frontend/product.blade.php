<x-frontend-layout>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap');

        .font-display {
            font-family: 'Bebas Neue', sans-serif;
        }

        .img-zoom {
            overflow: hidden;
        }

        .img-zoom img {
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .img-zoom:hover img {
            transform: scale(1.07);
        }

        .thumb-active {
            outline: 3px solid var(--primary);
            outline-offset: 3px;
        }

        .variant-chip {
            border: 2px solid #bbb;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .variant-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .variant-chip.selected {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }

        .variant-chip.oos {
            opacity: 0.35;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .btn-cart {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-cart::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(-100%) skewX(-15deg);
            transition: transform 0.4s ease;
        }

        .btn-cart:hover::after {
            transform: translateX(120%) skewX(-15deg);
        }

        .btn-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(215, 123, 49, 0.4);
        }

        .btn-cart:active {
            transform: translateY(0);
        }

        .btn-wish {
            transition: all 0.2s ease;
        }

        .btn-wish:hover {
            border-color: var(--primary) !important;
        }

        .btn-wish.wishlisted svg {
            fill: var(--primary);
            stroke: var(--primary);
        }

        .qty-btn {
            transition: all 0.15s ease;
        }

        .qty-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .tab-btn {
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .tab-btn.active {
            border-color: var(--primary);
            color: var(--primary);
        }

        .badge-sale {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .s-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .s-high {
            background: #22c55e;
            box-shadow: 0 0 6px rgba(34, 197, 94, .6);
        }

        .s-low {
            background: #f59e0b;
            box-shadow: 0 0 6px rgba(245, 158, 11, .6);
        }

        .s-out {
            background: #ef4444;
            box-shadow: 0 0 6px rgba(239, 68, 68, .6);
        }

        .cat-tag {
            background: rgba(215, 123, 49, .12);
            color: var(--primary);
            border: 1px solid rgba(215, 123, 49, .25);
        }

        .sg-card {
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        }

        .rel-card {
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .rel-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, .13);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp .55s ease both;
        }

        .delay-1 {
            animation-delay: .1s;
        }

        .delay-2 {
            animation-delay: .2s;
        }

        .delay-3 {
            animation-delay: .3s;
        }
    </style>

    {{-- ─── Breadcrumb ───────────────────────────────────────────── --}}
    <div class="container pt-5 pb-2">
        <nav class="flex items-center text-xs text-gray-500 gap-1 flex-wrap">
            <a href="#" class="hover:text-[--primary] transition-colors">Home</a>
            <span class="opacity-40 mx-1">/</span>
            <a href="#" class="hover:text-[--primary] transition-colors">Shop</a>
            <span class="opacity-40 mx-1">/</span>
            <a href="#" class="hover:text-[--primary] transition-colors">Sports Equipment</a>
            <span class="opacity-40 mx-1">/</span>
            <span class="font-medium text-gray-700">Pro Training Gloves X9</span>
        </nav>
    </div>

    {{-- ─── Main Product ─────────────────────────────────────────── --}}
    <main class="container py-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-16">

            {{-- ── Left: Gallery ─────────────────────────────── --}}
            <div class="fade-up">

                {{-- Main image --}}
                <div class="relative rounded-2xl overflow-hidden bg-white sg-card img-zoom aspect-square mb-3">
                    <span
                        class="badge-sale absolute top-4 left-4 z-10 px-3 py-1 rounded-full text-white text-sm font-display tracking-wide">
                        SALE 20%
                    </span>
                    <span
                        class="absolute top-4 right-4 z-10 bg-white rounded-full px-3 py-1 text-xs font-semibold text-gray-600 shadow">
                        NEW 2025
                    </span>
                    <img id="mainImage" src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=700&q=80"
                        alt="Pro Training Gloves X9" class="w-full h-full object-cover" />
                </div>

                {{-- Thumbnails --}}
                <div class="flex gap-3 overflow-x-auto pb-1">
                    <button
                        onclick="setImage(this,'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=700&q=80')"
                        class="thumb-active w-20 h-20 rounded-xl overflow-hidden bg-white sg-card shrink-0 img-zoom">
                        <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&q=60"
                            class="w-full h-full object-cover" alt="" />
                    </button>
                    <button
                        onclick="setImage(this,'https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=700&q=80')"
                        class="w-20 h-20 rounded-xl overflow-hidden bg-white sg-card shrink-0 img-zoom">
                        <img src="https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=200&q=60"
                            class="w-full h-full object-cover" alt="" />
                    </button>
                    <button
                        onclick="setImage(this,'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=700&q=80')"
                        class="w-20 h-20 rounded-xl overflow-hidden bg-white sg-card shrink-0 img-zoom">
                        <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=200&q=60"
                            class="w-full h-full object-cover" alt="" />
                    </button>
                    <button
                        onclick="setImage(this,'https://images.unsplash.com/photo-1609899464726-207e3a0f7297?w=700&q=80')"
                        class="w-20 h-20 rounded-xl overflow-hidden bg-white sg-card shrink-0 img-zoom">
                        <img src="https://images.unsplash.com/photo-1609899464726-207e3a0f7297?w=200&q=60"
                            class="w-full h-full object-cover" alt="" />
                    </button>
                </div>
            </div>

            {{-- ── Right: Info ────────────────────────────────── --}}
            <div class="fade-up delay-1 flex flex-col gap-5">

                {{-- Categories --}}
                <div class="flex flex-wrap gap-2">
                    <span class="cat-tag text-xs font-medium px-3 py-1 rounded-full">Sports Equipment</span>
                    <span class="cat-tag text-xs font-medium px-3 py-1 rounded-full">Training</span>
                    <span class="cat-tag text-xs font-medium px-3 py-1 rounded-full">Boxing</span>
                </div>

                {{-- Title --}}
                <div>
                    <h1 class="font-display text-5xl xl:text-6xl tracking-wide text-gray-900 leading-none">
                        PRO TRAINING <br>
                        <span style="color:var(--primary)">GLOVES X9</span>
                    </h1>
                    <p class="text-gray-500 text-sm mt-2">
                        SKU: <span id="currentSku" class="font-medium text-gray-700">PTG-X9-RED-M</span>
                    </p>
                </div>

                {{-- Rating --}}
                <div class="flex items-center gap-3">
                    <div class="flex gap-0.5">
                        @for ($i = 0; $i < 4; $i++)
                            <svg class="w-4 h-4" style="fill:var(--primary)" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-500">4.0 <span class="text-gray-400">(128 reviews)</span></span>
                </div>

                {{-- Price --}}
                <div class="flex items-end gap-3">
                    <span id="currentPrice" class="font-display text-5xl tracking-wide" style="color:var(--primary)">
                        79.99
                    </span>
                    <span class="text-xl text-gray-400 line-through mb-1">99.99</span>
                    <span class="mb-1 text-sm font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Save
                        20</span>
                </div>

                {{-- Stock --}}
                <div class="flex items-center gap-2">
                    <div id="stockDot" class="s-dot s-high"></div>
                    <span id="stockLabel" class="text-sm font-medium text-gray-700">In Stock</span>
                    <span id="stockQty" class="text-sm text-gray-400">— 14 units available</span>
                </div>

                <hr class="border-gray-200" />

                {{-- Color --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        Color: <span id="selectedColor" class="font-normal" style="color:var(--primary)">Red</span>
                    </p>
                    <div class="flex gap-2 flex-wrap" id="colorChips">
                        <button onclick="selectColor(this,'Red')" class="variant-chip selected w-9 h-9 rounded-full"
                            style="background:#ef4444;"></button>
                        <button onclick="selectColor(this,'Black')" class="variant-chip w-9 h-9 rounded-full"
                            style="background:#1f2937;"></button>
                        <button onclick="selectColor(this,'Blue')" class="variant-chip w-9 h-9 rounded-full"
                            style="background:#3b82f6;"></button>
                        <button onclick="selectColor(this,'White')"
                            class="variant-chip w-9 h-9 rounded-full border border-gray-200"
                            style="background:#f3f4f6;"></button>
                    </div>
                </div>

                {{-- Size --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm font-semibold text-gray-700">
                            Size: <span id="selectedSize" class="font-normal" style="color:var(--primary)">M</span>
                        </p>
                        <button class="text-xs underline text-gray-400 hover:text-gray-600">Size Guide</button>
                    </div>
                    <div class="flex gap-2 flex-wrap" id="sizeChips">
                        <button onclick="selectSize(this,'XS','PTG-X9-RED-XS',69.99,20)"
                            class="variant-chip px-4 py-2 rounded-lg text-sm font-medium text-gray-700">XS</button>
                        <button onclick="selectSize(this,'S','PTG-X9-RED-S',74.99,8)"
                            class="variant-chip px-4 py-2 rounded-lg text-sm font-medium text-gray-700">S</button>
                        <button onclick="selectSize(this,'M','PTG-X9-RED-M',79.99,14)"
                            class="variant-chip selected px-4 py-2 rounded-lg text-sm font-medium text-gray-700">M</button>
                        <button onclick="selectSize(this,'L','PTG-X9-RED-L',84.99,3)"
                            class="variant-chip px-4 py-2 rounded-lg text-sm font-medium text-gray-700">L</button>
                        <button onclick="selectSize(this,'XL','PTG-X9-RED-XL',89.99,0)"
                            class="variant-chip oos px-4 py-2 rounded-lg text-sm font-medium text-gray-400">XL</button>
                    </div>
                </div>

                {{-- Attribute pills --}}
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 bg-white rounded-lg px-3 py-2 sg-card">
                        <i class="fas fa-weight-hanging text-xs" style="color:var(--primary)"></i>
                        Weight: 0.35 kg
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 bg-white rounded-lg px-3 py-2 sg-card">
                        <i class="fas fa-tag text-xs" style="color:var(--primary)"></i>
                        Material: Leather
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 bg-white rounded-lg px-3 py-2 sg-card">
                        <i class="fas fa-bolt text-xs" style="color:var(--primary)"></i>
                        Grip: Enhanced
                    </div>
                </div>

                {{-- Qty + Cart --}}
                <div class="flex items-center gap-3 flex-wrap">

                    <div class="flex items-center bg-white rounded-xl sg-card overflow-hidden">
                        <button onclick="changeQty(-1)"
                            class="qty-btn w-11 h-11 flex items-center justify-center text-gray-600 font-bold text-lg">−</button>
                        <span id="qtyDisplay" class="w-10 text-center font-semibold text-gray-800 text-sm">1</span>
                        <button onclick="changeQty(1)"
                            class="qty-btn w-11 h-11 flex items-center justify-center text-gray-600 font-bold text-lg">+</button>
                    </div>

                    <button id="addToCartBtn" onclick="addToCart()"
                        class="btn-cart flex-1 min-w-45 h-12 rounded-xl text-white font-display tracking-widest text-lg flex items-center justify-center gap-2">
                        <i class="fas fa-cart-plus"></i>
                        Buy Now
                    </button>

                    <button id="wishBtn" onclick="toggleWish()"
                        class="btn-wish w-12 h-12 rounded-xl border-2 border-gray-200 bg-white flex items-center justify-center sg-card">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>

                {{-- Cart toast --}}

                {{-- <div id="cartToast" class="hidden text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-3 items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i>
                Item added to cart successfully!
            </div> --}}

                {{-- Trust badges --}}
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-white rounded-xl p-3 sg-card">
                        <i class="fas fa-shipping-fast text-base mb-1 block" style="color:var(--primary)"></i>
                        <p class="text-[11px] text-gray-500 leading-tight">Free Shipping<br><span
                                class="font-semibold text-gray-700">Orders 50+</span></p>
                    </div>
                    <div class="bg-white rounded-xl p-3 sg-card">
                        <i class="fas fa-undo-alt text-base mb-1 block" style="color:var(--primary)"></i>
                        <p class="text-[11px] text-gray-500 leading-tight">Easy Returns<br><span
                                class="font-semibold text-gray-700">30 Days</span></p>
                    </div>
                    <div class="bg-white rounded-xl p-3 sg-card">
                        <i class="fas fa-shield-alt text-base mb-1 block" style="color:var(--primary)"></i>
                        <p class="text-[11px] text-gray-500 leading-tight">Secure Pay<br><span
                                class="font-semibold text-gray-700">SSL Encrypted</span></p>
                    </div>
                </div>

            </div>{{-- end right --}}
        </div>{{-- end grid --}}

        {{-- ─── Tabs ─────────────────────────────────────────────── --}}
        <div class="mt-14 fade-up delay-2">
            <div class="flex gap-0 border-b border-gray-200 mb-6 overflow-x-auto">
                <button onclick="switchTab('description')" id="tab-description"
                    class="tab-btn active font-display tracking-widest text-base px-6 py-3 whitespace-nowrap">DESCRIPTION</button>
                <button onclick="switchTab('specs')" id="tab-specs"
                    class="tab-btn font-display tracking-widest text-base px-6 py-3 text-gray-500 whitespace-nowrap">SPECIFICATIONS</button>
                <button onclick="switchTab('variants')" id="tab-variants"
                    class="tab-btn font-display tracking-widest text-base px-6 py-3 text-gray-500 whitespace-nowrap">VARIANTS</button>
                <button onclick="switchTab('reviews')" id="tab-reviews"
                    class="tab-btn font-display tracking-widest text-base px-6 py-3 text-gray-500 whitespace-nowrap">REVIEWS
                    (128)</button>
            </div>

            {{-- Description --}}
            <div id="panel-description" class="tab-panel">
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-3 text-sm text-gray-600 leading-relaxed">
                        <p>The <strong>Pro Training Gloves X9</strong> are engineered for serious athletes who demand
                            performance, protection, and durability. Crafted from premium full-grain leather with an
                            anatomically contoured fit, these gloves deliver unmatched wrist support and knuckle
                            protection.</p>
                        <p>Whether you're training at the bag, sparring, or competing, the X9's triple-layer foam
                            padding and moisture-wicking inner lining keep you comfortable and focused every session.
                        </p>
                    </div>
                    <ul class="space-y-3">
                        @foreach (['Triple-layer premium foam for superior knuckle protection', 'Full-grain leather exterior for long-lasting durability', 'Moisture-wicking antimicrobial lining', 'Reinforced thumb attachment and wrist stabilisation', 'Wide Velcro strap for adjustable compression'] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <span class="w-2 h-2 rounded-full mt-1.5 shrink-0"
                                    style="background:var(--primary)"></span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Specifications --}}
            <div id="panel-specs" class="tab-panel hidden">
                <div class="overflow-hidden rounded-2xl sg-card bg-white">
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach ([['SKU', 'PTG-X9-RED-M'], ['Weight', '0.35 kg'], ['Material', 'Full-grain Leather'], ['Padding', 'Triple-Layer EVA Foam'], ['Closure', 'Wide Hook & Loop Velcro'], ['Sizes', 'XS, S, M, L (XL — Out of Stock)'], ['Colors', 'Red, Black, Blue, White'], ['Stock', '14 units (M / Red)']] as $i => [$label, $value])
                                <tr
                                    class="border-b border-gray-100 last:border-0 {{ $i % 2 !== 0 ? 'bg-gray-50' : '' }}">
                                    <td class="py-4 px-6 font-semibold text-gray-700 w-1/3">{{ $label }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Variants --}}
            <div id="panel-variants" class="tab-panel hidden">
                <div class="overflow-hidden rounded-2xl sg-card bg-white">
                    <table class="w-full text-sm">
                        <thead style="background:var(--primary)">
                            <tr>
                                <th class="py-3 px-5 text-left text-white font-display tracking-widest text-xs">VARIANT
                                </th>
                                <th class="py-3 px-5 text-left text-white font-display tracking-widest text-xs">SKU
                                </th>
                                <th class="py-3 px-5 text-right text-white font-display tracking-widest text-xs">PRICE
                                </th>
                                <th class="py-3 px-5 text-right text-white font-display tracking-widest text-xs">STOCK
                                </th>
                                <th class="py-3 px-5 text-right text-white font-display tracking-widest text-xs">WEIGHT
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([['Red / XS', 'PTG-X9-RED-XS', '$69.99', 20, '0.30 kg'], ['Red / S', 'PTG-X9-RED-S', '74.99', 8, '0.32 kg'], ['Red / M', 'PTG-X9-RED-M', '79.99', 14, '0.35 kg'], ['Red / L', 'PTG-X9-RED-L', '84.99', 3, '0.38 kg'], ['Red / XL', 'PTG-X9-RED-XL', '89.99', 0, '0.40 kg'], ['Black / M', 'PTG-X9-BLK-M', '79.99', 11, '0.35 kg'], ['Blue / M', 'PTG-X9-BLU-M', '$79.99', 6, '0.35 kg']] as $i => [$name, $sku, $price, $stock, $weight])
                                <tr
                                    class="border-b border-gray-100 last:border-0 {{ $i % 2 !== 0 ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-5 font-medium text-gray-700">{{ $name }}</td>
                                    <td class="py-3 px-5 text-gray-500 font-mono text-xs">{{ $sku }}</td>
                                    <td class="py-3 px-5 text-right font-semibold" style="color:var(--primary)">
                                        {{ $price }}</td>
                                    <td class="py-3 px-5 text-right">
                                        @if ($stock > 5)
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $stock }}</span>
                                        @elseif($stock > 0)
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">{{ $stock }}
                                                low</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Out
                                                of stock</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-5 text-right text-gray-500">{{ $weight }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Reviews --}}
            <div id="panel-reviews" class="tab-panel hidden space-y-4">
                @foreach ([['JD', 'James D.', 'Mar 2025', 5, 'style="background:var(--primary)"', 'Best training gloves I\'ve ever used. Leather quality is top-notch and wrist support is incredible. Used them daily for 3 months and they still look brand new.'], ['SR', 'Sara R.', 'Feb 2025', 4, 'class="bg-blue-500"', 'Great fit for medium hands. Stitching is solid and padding feels premium. Slight break-in period but totally worth it. Fast delivery too!'], ['MK', 'Mike K.', 'Jan 2025', 5, 'class="bg-emerald-500"', 'Bought for my boxing classes. Exactly what I needed — durable, comfortable, and they look amazing in red.']] as [$initials, $name, $date, $stars, $avatarAttr, $review])
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-5 sg-card">
                        <div {{ $avatarAttr }}
                            class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center font-bold text-white text-sm">
                            {{ $initials }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-semibold text-gray-800 text-sm">{{ $name }}</p>
                                <span class="text-xs text-gray-400">{{ $date }}</span>
                            </div>
                            <div class="flex gap-0.5 mb-2">
                                @for ($s = 0; $s < $stars; $s++)
                                    <svg class="w-3.5 h-3.5" style="fill:var(--primary)" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                                @for ($s = $stars; $s < 5; $s++)
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600">{{ $review }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ─── Related Products ─────────────────────────────────── --}}
        <section class="mt-16 fade-up delay-3">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display text-4xl tracking-widest text-gray-900">
                    RELATED <span style="color:var(--primary)">PRODUCTS</span>
                </h2>
                <a href="#"
                    class="text-sm font-medium underline text-gray-500 hover:text-[--primary] transition-colors">
                    View All
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                @foreach ([['https://images.unsplash.com/photo-1592480090476-c5efbe70a4e6?w=400&q=70', 'Training', 'Speed Jump Rope Pro', '24.99'], ['https://images.unsplash.com/photo-1552084117-56a987666449?w=400&q=70', 'Accessories', 'Pro Hand Wraps 5m', '12.00'], ['https://images.unsplash.com/photo-1518611012118-696072aa579a?w=400&q=70', 'Fitness', 'Resistance Bands Set', '34.99'], ['https://images.unsplash.com/photo-1601422407692-ec4eeec1d9b3?w=400&q=70', 'Equipment', 'Heavy Bag 60kg', '189.00']] as [$img, $cat, $name, $price])
                    <a href="#" class="rel-card bg-white rounded-2xl overflow-hidden sg-card block">
                        <div class="img-zoom aspect-square overflow-hidden">
                            <img src="{{ $img }}" alt="{{ $name }}"
                                class="w-full h-full object-cover" />
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-gray-400 mb-0.5">{{ $cat }}</p>
                            <p class="font-semibold text-gray-800 text-sm leading-tight">{{ $name }}</p>
                            <p class="text-sm font-display tracking-wide mt-1" style="color:var(--primary)">
                                {{ $price }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

    </main>

    <script>
        // ── Gallery ────────────────────────────────────────────────
        function setImage(btn, src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumb-active').forEach(b => b.classList.remove('thumb-active'));
            btn.classList.add('thumb-active');
        }

        // ── Qty stepper ───────────────────────────────────────────
        let qty = 1;

        function changeQty(delta) {
            qty = Math.max(1, qty + delta);
            document.getElementById('qtyDisplay').textContent = qty;
        }

        // ── Color chips ───────────────────────────────────────────
        function selectColor(btn, name) {
            document.querySelectorAll('#colorChips button').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            document.getElementById('selectedColor').textContent = name;
        }

        // ── Size chips ────────────────────────────────────────────
        function selectSize(btn, size, sku, price, stock) {
            if (btn.classList.contains('oos')) return;
            document.querySelectorAll('#sizeChips button').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            document.getElementById('selectedSize').textContent = size;
            document.getElementById('currentSku').textContent = sku;
            document.getElementById('currentPrice').textContent = '$' + price.toFixed(2);
            updateStock(stock);
        }

        function updateStock(stock) {
            const dot = document.getElementById('stockDot');
            const label = document.getElementById('stockLabel');
            const sqty = document.getElementById('stockQty');
            const btn = document.getElementById('addToCartBtn');
            dot.className = 's-dot';
            if (stock === 0) {
                dot.classList.add('s-out');
                label.textContent = 'Out of Stock';
                sqty.textContent = '';
                btn.disabled = true;
                btn.style.opacity = '0.5';
            } else if (stock <= 5) {
                dot.classList.add('s-low');
                label.textContent = 'Low Stock';
                sqty.textContent = `— only ${stock} left!`;
                btn.disabled = false;
                btn.style.opacity = '1';
            } else {
                dot.classList.add('s-high');
                label.textContent = 'In Stock';
                sqty.textContent = `— ${stock} units available`;
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        }

        // ── Wishlist ──────────────────────────────────────────────
        let wished = false;

        function toggleWish() {
            wished = !wished;
            const btn = document.getElementById('wishBtn');
            const svg = btn.querySelector('svg');
            btn.classList.toggle('wishlisted', wished);
            svg.style.fill = wished ? 'var(--primary)' : 'none';
            svg.style.stroke = wished ? 'var(--primary)' : 'currentColor';
        }

        // ── Add to cart ───────────────────────────────────────────
        function addToCart() {
            const toast = document.getElementById('cartToast');
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('flex');
            }, 3000);
        }

        // ── Tabs ──────────────────────────────────────────────────
        const TABS = ['description', 'specs', 'variants', 'reviews'];

        function switchTab(name) {
            TABS.forEach(t => {
                document.getElementById('panel-' + t)?.classList.add('hidden');
                const b = document.getElementById('tab-' + t);
                if (b) {
                    b.classList.remove('active');
                    b.classList.add('text-gray-500');
                }
            });
            document.getElementById('panel-' + name)?.classList.remove('hidden');
            const a = document.getElementById('tab-' + name);
            if (a) {
                a.classList.add('active');
                a.classList.remove('text-gray-500');
            }
        }
    </script>

</x-frontend-layout>
