<x-frontend.layout>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                    <p class="text-gray-500 text-sm">Track your order status & summary</p>
                </div>

                <a href="{{ route('orders.my') }}"
                    class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 font-medium">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </a>
            </div>

            @php
                $allStatuses = App\Models\Status::orderBy('order')->get();

                $statusColorMap = [
                    'Pending' => 'bg-yellow-100 text-yellow-800',
                    'Confirmed' => 'bg-blue-100 text-blue-800',
                    'Processing' => 'bg-purple-100 text-purple-800',
                    'Shipped' => 'bg-indigo-100 text-indigo-800',
                    'Delivered' => 'bg-green-100 text-green-800',
                    'Cancelled' => 'bg-red-100 text-red-800',
                    'Returned' => 'bg-gray-100 text-gray-800',
                ];

                $currentStatusName = $order->status->name ?? 'Pending';
                $currentColor = $statusColorMap[$currentStatusName] ?? 'bg-gray-100 text-gray-800';

                $timelineStatuses = $allStatuses->filter(fn($s) => !in_array($s->name, ['Cancelled', 'Returned']));
            @endphp

            <!-- STATUS CARD -->
            <div class="bg-white rounded-2xl shadow-md mb-6 overflow-hidden">

                <div class="px-6 py-5 border-b flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Order #{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-400">
                            <i class="fa-regular fa-calendar"></i>
                            {{ $order->created_at->format('F j, Y') }}
                        </p>
                    </div>

                    <span class="px-4 py-1 text-sm font-semibold rounded-full {{ $currentColor }}">
                        {{ $currentStatusName }}
                    </span>
                </div>

                <!-- Timeline -->
                <div class="px-6 py-8">

                    @if (!in_array($currentStatusName, ['Cancelled', 'Returned']))

                        <div class="flex justify-between items-center relative">

                            <!-- Line -->
                            <div class="absolute top-4 left-0 w-full h-1 bg-gray-200"></div>

                            @foreach ($timelineStatuses as $step)
                                @php
                                    $isDone = $order->status->order >= $step->order;
                                @endphp

                                <div class="relative z-10 text-center flex-1">

                                    <div
                                        class="w-10 h-10 mx-auto rounded-full flex items-center justify-center shadow
                                        {{ $isDone ? 'bg-orange-500 text-white' : 'bg-gray-300 text-white' }}">

                                        @if ($isDone)
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            <i class="fa-solid fa-circle"></i>
                                        @endif

                                    </div>

                                    <p
                                        class="text-xs mt-2 font-medium {{ $isDone ? 'text-gray-800' : 'text-gray-400' }}">
                                        {{ $step->name }}
                                    </p>

                                </div>
                            @endforeach

                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="fa-solid fa-triangle-exclamation text-3xl text-red-400 mb-2"></i>
                            <p class="text-gray-600">
                                This order has been
                                <span class="font-semibold text-red-600">{{ strtolower($currentStatusName) }}</span>.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            <!-- ITEMS -->
            <div class="bg-white rounded-2xl shadow-md mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-box"></i> Order Items
                </div>

                <div class="divide-y">
                    @foreach ($order->items as $item)
                        @php
                            $product = $item->product;
                            $variant = $item->productVariant;

                            $image =
                                optional($product->images)->where('is_primary', true)->first() ??
                                $variant?->images->first();

                            $itemTotal = $item->price * $item->quantity;
                        @endphp

                        <div class="p-5 flex gap-4 items-center hover:bg-gray-50 transition">

                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ $image ? asset('storage/' . $image->image_path) : asset('images/placeholder.png') }}"
                                    class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>

                                @if ($variant?->name)
                                    <p class="text-sm text-gray-500">{{ $variant->name }}</p>
                                @endif

                                <p class="text-sm text-gray-500 mt-1">
                                    Qty {{ $item->quantity }} × Rs. {{ number_format($item->price, 2) }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-lg font-bold text-orange-600">
                                    Rs. {{ number_format($itemTotal, 2) }}
                                </p>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- SHIPPING + PAYMENT -->
            <div class="grid md:grid-cols-2 gap-6">

                <!-- Shipping -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h3 class="font-semibold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot"></i> Shipping Info
                    </h3>

                    <div class="text-sm text-gray-600 space-y-2">
                        <p class="font-medium text-gray-800">
                            {{ $order->address->name ?? auth()->user()->name }}
                        </p>
                        <p>{{ $order->address->phone_no }}</p>
                        <p>
                            {{ $order->address->address_line1 }},
                            {{ optional($order->address->district)->name }},
                            {{ optional($order->address->province)->name }}
                        </p>
                    </div>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-2xl shadow-md p-6">

                    <h3 class="font-semibold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-receipt"></i> Payment Summary
                    </h3>

                    <div class="space-y-2 text-sm">

                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span>Rs. {{ number_format($order->sub_total, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span>{{ $order->shipping_fee > 0 ? 'Rs. ' . number_format($order->shipping_fee, 2) : 'Free' }}</span>
                        </div>

                        <div class="border-t my-2"></div>

                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-orange-600 text-xl">
                                Rs. {{ number_format($order->total, 2) }}
                            </span>
                        </div>

                    </div>

                    @if (in_array($currentStatusName, ['Pending', 'Confirmed']))
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-5">
                            @csrf
                            @method('PUT')

                            <button
                                class="w-full bg-red-600 text-white py-2 rounded-xl hover:bg-red-700 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-trash"></i>
                                Cancel Order
                            </button>
                        </form>
                    @endif

                </div>

            </div>

            <!-- SUPPORT -->
            <div class="mt-8 bg-gradient-to-r from-orange-50 to-orange-100 p-6 rounded-2xl text-center">
                <p class="text-gray-600 mb-2">Need help with your order?</p>
                <a href="{{ route('contact.index') }}"
                    class="text-orange-600 font-semibold hover:underline inline-flex items-center gap-2">
                    Contact Support
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>

</x-frontend.layout>
