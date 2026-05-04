<x-frontend.layout>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 p-10 text-center">
                    <div class="w-20 h-20 mx-auto rounded-full bg-green-500 flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-check text-white text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mt-5">
                        Order Placed Successfully
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Thank you! Your order has been confirmed and is being processed.
                    </p>
                </div>

                <div class="p-8">

                    <div class="bg-gray-50 rounded-xl p-5 mb-6 border">

                        <h3 class="text-sm font-semibold text-gray-500 mb-4 uppercase tracking-wide">
                            Order Summary
                        </h3>

                        <div class="space-y-2 text-sm">

                            <div class="flex justify-between">
                                <span class="text-gray-500">Order Number</span>
                                <span class="font-medium text-gray-800">
                                    #{{ $order->order_number }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Date</span>
                                <span>{{ $order->created_at->format('F j, Y') }}</span>
                            </div>

                            <div class="border-t my-2"></div>

                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-800">Total Paid</span>
                                <span class="text-xl font-bold text-orange-600">
                                    Rs. {{ number_format($order->total, 2) }}
                                </span>
                            </div>

                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-box"></i> Items
                        </h3>

                        <div class="space-y-2">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between text-sm bg-white border rounded-lg px-3 py-2">
                                    <span class="text-gray-700">
                                        {{ $item->product->name }} × {{ $item->quantity }}
                                    </span>
                                    <span class="font-medium text-gray-800">
                                        Rs. {{ number_format($item->price * $item->quantity, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-5 mb-6 border border-blue-100">
                        <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-blue-500"></i>
                            Shipping Address
                        </h3>

                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $order->address->address_line1 }}
                            @if ($order->address->address_line2)
                                , {{ $order->address->address_line2 }}
                            @endif
                            , {{ $order->address->district->name ?? '' }},
                            {{ $order->address->province->name ?? '' }}

                            @if ($order->address->nearest_landmark)
                                <br><span class="text-gray-500">(Near: {{ $order->address->nearest_landmark }})</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">

                        <a href="{{ route('orders.show', $order) }}"
                            class="flex-1 inline-flex justify-center items-center gap-2 bg-orange-600 text-white py-3 rounded-xl font-semibold hover:bg-orange-700 transition shadow-sm">
                            <i class="fa-solid fa-eye"></i>
                            View Order
                        </a>

                        <a href="{{ route('products.index') }}"
                            class="flex-1 inline-flex justify-center items-center gap-2 border border-orange-600 text-orange-600 py-3 rounded-xl font-semibold hover:bg-orange-50 transition">
                            <i class="fa-solid fa-store"></i>
                            Continue Shopping
                        </a>

                    </div>

                </div>

            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                    <i class="fa-regular fa-envelope"></i>
                    A confirmation email has been sent to your email address.
                </p>
            </div>

        </div>
    </div>

</x-frontend.layout>
