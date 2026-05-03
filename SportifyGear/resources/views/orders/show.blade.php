<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                <a href="{{ route('orders.my') }}" class="text-orange-600 hover:text-orange-700">
                    ← Back to My Orders
                </a>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Order #{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-500">
                            Placed on {{ $order->created_at->format('F j, Y') }}
                        </p>
                    </div>

                    @php
                        $statusColors = [
                            1 => 'bg-yellow-100 text-yellow-800',
                            2 => 'bg-blue-100 text-blue-800',
                            3 => 'bg-green-100 text-green-800',
                            4 => 'bg-red-100 text-red-800',
                        ];
                    @endphp

                    <span
                        class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status_id] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $order->status->name ?? 'Pending' }}
                    </span>
                </div>

                <!-- Timeline -->
                <div class="px-6 py-6 border-b">
                    <div class="flex items-center justify-between relative">

                        <!-- Line -->
                        <div class="absolute top-4 left-0 w-full h-1 bg-gray-200"></div>

                        @php
                            $steps = [
                                1 => 'Placed',
                                2 => 'Confirmed',
                                3 => 'Shipped',
                                4 => 'Delivered',
                            ];
                        @endphp

                        @foreach ($steps as $step => $label)
                            <div class="relative z-10 text-center flex-1">
                                <div
                                    class="w-8 h-8 mx-auto rounded-full flex items-center justify-center
                                    {{ $order->status_id >= $step ? 'bg-orange-500 text-white' : 'bg-gray-300 text-white' }}">
                                    ✓
                                </div>
                                <p class="text-xs mt-2">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-800">Order Items</h3>
                </div>

                @if ($order->items->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        No items found in this order.
                    </div>
                @endif

                <div class="divide-y">
                    @foreach ($order->items as $item)
                        @php
                            $product = $item->product;
                            $variant = $item->productVariant;

                            $image = optional($product->images)->where('is_primary', true)->first();

                            if (!$image && $variant && $variant->images->isNotEmpty()) {
                                $image = $variant->images->first();
                            }
                        @endphp

                        <div class="p-6 flex gap-4">
                            <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ $image ? asset('storage/' . $image->image_path) : asset('images/placeholder.png') }}"
                                    class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1 flex justify-between">
                                <div>
                                    <h3 class="font-semibold">{{ $product->name }}</h3>

                                    @if ($variant?->name)
                                        <p class="text-sm text-gray-500">Variant: {{ $variant->name }}</p>
                                    @endif

                                    @if ($variant && $variant->attributeValues->isNotEmpty())
                                        <p class="text-xs text-gray-500 mt-1">
                                            @foreach ($variant->attributeValues as $attr)
                                                {{ $attr->attribute->name }}: {{ $attr->value }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <p>Qty: {{ $item->quantity }}</p>
                                    <p class="text-orange-600 font-semibold">
                                        Rs. {{ number_format($item->price, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Total: Rs. {{ number_format($item->price * $item->quantity, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping + Payment -->
            <div class="grid md:grid-cols-2 gap-6">

                <!-- Shipping -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="font-semibold">Shipping Information</h3>
                    </div>

                    <div class="p-6 text-sm text-gray-600">
                        @if ($order->address)
                            <p class="font-medium text-gray-800">
                                {{ $order->address->name ?? auth()->user()->name }}
                            </p>

                            <p>{{ $order->address->phone_no }}</p>

                            <p class="mt-2">
                                {{ $order->address->address_line1 }}
                                {{ $order->address->address_line2 ? ', ' . $order->address->address_line2 : '' }}
                                , {{ optional($order->address->district)->name }}
                                , {{ optional($order->address->province)->name }}
                            </p>

                            @if ($order->address->nearest_landmark)
                                <p>Near: {{ $order->address->nearest_landmark }}</p>
                            @endif
                        @else
                            <p class="text-red-500">No address found</p>
                        @endif
                    </div>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="font-semibold">Payment Summary</h3>
                    </div>

                    <div class="p-6 text-sm space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rs. {{ number_format($order->sub_total, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>
                                {{ $order->shipping_fee > 0 ? 'Rs. ' . number_format($order->shipping_fee, 2) : 'Free' }}
                            </span>
                        </div>

                        @if ($order->coupon)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>
                                    -Rs.
                                    {{ number_format($order->sub_total + $order->shipping_fee - $order->total, 2) }}
                                </span>
                            </div>
                        @endif

                        <div class="border-t pt-2 flex justify-between font-bold">
                            <span>Total</span>
                            <span class="text-orange-600">
                                Rs. {{ number_format($order->total, 2) }}
                            </span>
                        </div>

                        @if (in_array($order->status_id, [1, 2]))
                            <form method="POST" action="{{ route('orders.cancel', $order) }}"
                                onsubmit="return confirm('Cancel this order?')">
                                @csrf
                                @method('PUT')

                                <button class="mt-4 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="bg-blue-50 rounded-xl p-6 mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Need help?</p>
                <a href="{{ route('contact.index') }}" class="text-orange-600 font-medium">
                    Contact Support →
                </a>
            </div>

        </div>
    </div>
</x-frontend.layout>
