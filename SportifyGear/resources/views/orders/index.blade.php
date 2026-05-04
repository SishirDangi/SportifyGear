<x-frontend.layout>
    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-10 flex justify-between items-end">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">My Orders</h1>
                    <p class="text-gray-500 mt-1">Track and manage your purchases</p>
                </div>
            </div>

            @php
                $statusColors = [
                    'Pending' => 'bg-amber-100 text-amber-800',
                    'Confirmed' => 'bg-blue-100 text-blue-800',
                    'Processing' => 'bg-purple-100 text-purple-800',
                    'Shipped' => 'bg-indigo-100 text-indigo-800',
                    'Delivered' => 'bg-emerald-100 text-emerald-800',
                    'Cancelled' => 'bg-rose-100 text-rose-800',
                    'Returned' => 'bg-gray-100 text-gray-800',
                ];
            @endphp

            @if ($orders->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-3xl shadow-lg p-12 text-center">
                    <div
                        class="w-24 h-24 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mb-6">
                        <i class="fa-solid fa-cart-shopping text-4xl text-orange-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">No Orders Yet</h2>
                    <p class="text-gray-500 mb-6">Start shopping to see your orders here</p>

                    <a href="{{ route('products.index') }}"
                        class="bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-700 transition">
                        Start Shopping
                    </a>
                </div>
            @else
                <div class="space-y-8">
                    @foreach ($orders as $order)
                        @php
                            $statusName = $order->status->name ?? 'Pending';
                            $statusColor = $statusColors[$statusName] ?? 'bg-gray-100 text-gray-800';

                            $orderSubtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
                            $shippingFee = $order->shipping_fee ?? 0;
                            $orderTotal = $order->total ?? $orderSubtotal + $shippingFee;
                            $itemCount = $order->items->count();
                        @endphp

                        <!-- Order Card -->
                        <div class="bg-white rounded-2xl shadow-sm border hover:shadow-lg transition">

                            <!-- Header -->
                            <div class="p-6 border-b flex justify-between flex-wrap gap-4">
                                <div>
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">
                                            #{{ $order->order_number }}
                                        </span>

                                        <span class="text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </span>

                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">
                                            {{ $itemCount }} {{ Str::plural('item', $itemCount) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusColor }}">
                                        {{ $statusName }}
                                    </span>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="divide-y">
                                @foreach ($order->items as $item)
                                    @php
                                        $variant = $item->productVariant;
                                        $product = $item->product;
                                        $image =
                                            optional($product->images)->where('is_primary', true)->first() ??
                                            $variant?->images->first();

                                        $itemTotal = $item->price * $item->quantity;
                                    @endphp

                                    <div class="p-5 flex gap-4 items-center">

                                        <!-- Image -->
                                        <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                            <img src="{{ $image ? asset('storage/' . $image->image_path) : asset('images/placeholder.png') }}"
                                                class="w-full h-full object-cover">
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>

                                            @if ($variant?->name)
                                                <p class="text-sm text-gray-500">{{ $variant->name }}</p>
                                            @endif

                                            <div class="text-sm mt-2 text-gray-600">
                                                Qty: {{ $item->quantity }} × Rs. {{ number_format($item->price, 2) }}
                                            </div>
                                        </div>

                                        <!-- Total -->
                                        <div class="text-right">
                                            <p class="font-bold text-orange-600 text-lg">
                                                Rs. {{ number_format($itemTotal, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Bottom Section -->
                            <div class="p-6 bg-gray-50 flex flex-col lg:flex-row gap-6 justify-between">

                                <!-- 💰 Price Summary -->
                                <div class="bg-white rounded-xl border p-5 w-full max-w-sm">

                                    <h4 class="text-sm font-semibold text-gray-500 mb-4 uppercase">
                                        Payment Summary
                                    </h4>

                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Subtotal</span>
                                            <span>Rs. {{ number_format($orderSubtotal, 2) }}</span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Shipping Fee</span>
                                            <span>
                                                @if ($shippingFee > 0)
                                                    Rs. {{ number_format($shippingFee, 2) }}
                                                @else
                                                    <span class="text-green-600 font-medium">Free</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="border-t my-2"></div>

                                        <div class="flex justify-between font-bold text-lg">
                                            <span>Total</span>
                                            <span class="text-orange-600 text-xl">
                                                Rs. {{ number_format($orderTotal, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-3 justify-center">

                                    <a href="{{ route('orders.show', $order) }}"
                                        class="px-5 py-2 bg-white border rounded-xl hover:bg-gray-100 text-center">
                                        View Details
                                    </a>

                                    @if (in_array($statusName, ['Pending', 'Confirmed']))
                                        <form method="POST" action="{{ route('orders.cancel', $order) }}">
                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                class="px-5 py-2 bg-red-50 border border-red-200 text-red-600 rounded-xl hover:bg-red-100 w-full">
                                                Cancel Order
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $orders->links() }}
                </div>

            @endif

        </div>
    </div>
</x-frontend.layout>
