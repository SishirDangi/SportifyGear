<style>
    .container {
        display: flex;
        flex-direction: column;
        gap: 18px;
        max-width: 1200px;
        margin: auto;
        padding: 10px;
    }

    .card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .card-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 16px;
        color: #1f2937;
    }

    .card-body {
        padding: 14px 16px;
    }

    /* TABLE RESPONSIVE */
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        max-height: 380px;
        border-radius: 10px;
    }

    .table {
        width: 100%;
        min-width: 700px;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table thead {
        background: #f9fafb;
        text-transform: uppercase;
        font-size: 11px;
        color: #6b7280;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table th,
    .table td {
        padding: 10px 14px;
        white-space: nowrap;
    }

    .table tbody tr {
        border-top: 1px solid #e5e7eb;
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background: #f9fafb;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .bold {
        font-weight: 600;
    }

    .muted {
        color: #6b7280;
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }

    @media (min-width: 768px) {
        .grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .info-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .divider {
        border-top: 1px solid #e5e7eb;
        margin: 10px 0;
    }

    .small {
        font-size: 13px;
        line-height: 1.6;
    }

    .italic {
        font-style: italic;
    }

    /* MOBILE OPTIMIZATION */
    @media (max-width: 640px) {

        .card-header {
            font-size: 15px;
            padding: 10px 14px;
        }

        .card-body {
            padding: 12px 14px;
        }

        .info-group {
            flex-direction: column;
            align-items: flex-start;
        }

        .table {
            font-size: 12px;
        }

        .table th,
        .table td {
            padding: 8px 10px;
        }

        .table-wrapper {
            max-height: 320px;
        }
    }

    /* DARK MODE */
    html.dark .card {
        background: #1f2937;
        border-color: #374151;
        color: #e5e7eb;
    }

    html.dark .card-header {
        color: #f9fafb;
        border-color: #374151;
    }

    html.dark .muted {
        color: #9ca3af;
    }

    html.dark .table thead {
        background: #374151;
        color: #d1d5db;
    }

    html.dark .table tbody tr {
        border-top-color: #374151;
    }

    html.dark .table tbody tr:hover {
        background: #374151;
    }

    html.dark .table td,
    html.dark .table th {
        color: #e5e7eb;
    }

    html.dark .divider {
        border-top-color: #374151;
    }

    html.dark .badge-success {
        background: #065f46;
        color: #d1fae5;
    }

    html.dark .badge-warning {
        background: #92400e;
        color: #fef3c7;
    }

    html.dark .badge-danger {
        background: #991b1b;
        color: #fee2e2;
    }

    html.dark .info-group span {
        color: #e5e7eb;
    }

    html.dark .info-group .muted {
        color: #9ca3af;
    }
</style>

<div class="container">

    {{-- ITEMS --}}
    <div class="card">
        <div class="card-header">Order Items</div>

        @if ($order->items->isNotEmpty())
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="bold">
                                    {{ $item->product_name ?? optional($item->product)->name }}
                                </td>
                                <td class="muted">
                                    {{ optional($item->productVariant)->name ?? '—' }}
                                </td>
                                <td class="text-center bold">
                                    {{ $item->quantity }}
                                </td>
                                <td class="text-right">
                                    Rs {{ number_format($item->price, 2) }}
                                </td>
                                <td class="text-right bold">
                                    Rs {{ number_format($item->quantity * $item->price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body muted">No items found.</div>
        @endif
    </div>

    {{-- ORDER SUMMARY --}}
    <div class="card">
        <div class="card-header">Order Summary</div>
        <div class="card-body small">
            @php
                $subtotal =
                    $order->subtotal ??
                    $order->items->sum(function ($item) {
                        return $item->quantity * $item->price;
                    });

                $shipping =
                    $order->shipping_fee ?? (optional(optional($order->address)->shippingZone)->shipping_fee ?? 0);

                $total = $order->total ?? $subtotal + $shipping;
            @endphp

            <div class="info-group">
                <span class="muted">Subtotal</span>
                <span class="bold">Rs {{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="info-group">
                <span class="muted">Shipping Fee</span>
                <span class="bold">Rs {{ number_format($shipping, 2) }}</span>
            </div>
            <div class="divider"></div>
            <div class="info-group">
                <span class="bold">Total</span>
                <span class="bold">Rs {{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ADDRESS + PAYMENT --}}
    <div class="grid">

        {{-- ADDRESS --}}
        <div class="card">
            <div class="card-header">Shipping Address</div>
            <div class="card-body small">

                @if ($order->address)
                    <p class="bold">{{ $order->address->name }}</p>
                    <p>{{ $order->address->phone_no }}</p>
                    <p>{{ $order->address->email }}</p>

                    <div class="divider"></div>

                    <p>
                        {{ $order->address->address_line1 }},
                        {{ $order->address->address_line2 }}
                    </p>
                    <p>
                        {{ optional($order->address->district)->name }},
                        {{ optional($order->address->province)->name }}
                    </p>

                    @if ($order->address->nearest_landmark)
                        <p class="italic muted">
                            {{ $order->address->nearest_landmark }}
                        </p>
                    @endif
                @else
                    <p class="muted">No address on file.</p>
                @endif

            </div>
        </div>

        {{-- PAYMENT --}}
        <div class="card">
            <div class="card-header">Payment Details</div>
            <div class="card-body small">

                @php
                    $latestPayment = $order->payments->sortByDesc('created_at')->first();
                @endphp

                @if ($latestPayment)
                    <div class="info-group">
                        <span class="muted">Method</span>
                        <span class="bold">{{ ucfirst($latestPayment->method) }}</span>
                    </div>

                    <div class="info-group">
                        <span class="muted">Transaction</span>
                        <span>{{ $latestPayment->transaction_id ?? '—' }}</span>
                    </div>

                    <div class="info-group">
                        <span class="muted">Amount</span>
                        <span class="bold">Rs {{ number_format($latestPayment->amount, 2) }}</span>
                    </div>

                    <div class="info-group">
                        <span class="muted">Status</span>

                        <span
                            class="badge
                            @if ($latestPayment->status === 'Paid') badge-success
                            @elseif($latestPayment->status === 'Pending') badge-warning
                            @else badge-danger @endif">
                            {{ $latestPayment->status }}
                        </span>
                    </div>

                    <div class="info-group">
                        <span class="muted">Paid At</span>
                        <span>
                            {{ $latestPayment->paid_at ? \Carbon\Carbon::parse($latestPayment->paid_at)->format('M j, Y H:i') : '—' }}
                        </span>
                    </div>
                @else
                    <p class="muted">No payment yet.</p>
                @endif

            </div>
        </div>

    </div>

</div>
