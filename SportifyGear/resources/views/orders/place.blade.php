<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Complete Your Order</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('orders.store') }}" id="directOrderForm">
                        @csrf
                        <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">

                        <!-- Address Section with Add/Manage buttons -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Shipping Address</h2>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Address</label>
                                <select name="address_id" id="address_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Select an address</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->address_line1 }}
                                            @if ($address->address_line2)
                                                , {{ $address->address_line2 }}
                                            @endif
                                            , {{ $address->district?->name }}, {{ $address->province?->name }}
                                            @if ($address->nearest_landmark)
                                                (Near: {{ $address->nearest_landmark }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-center flex justify-center gap-4">
                                <a href="#" id="openAddressModalBtn"
                                    class="text-orange-600 hover:text-orange-700 text-sm">
                                    + Add New Address
                                </a>
                                <a href="#" id="manageAddressesBtn"
                                    class="text-blue-600 hover:text-blue-700 text-sm">
                                    Manage Addresses
                                </a>
                            </div>
                            <div id="addressSuccessMessage" class="mt-3 text-green-600 text-sm hidden"></div>
                        </div>

                        <!-- Quantity Section -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Quantity</h2>
                            <div class="flex items-center gap-4">
                                <button type="button" onclick="changeQty(-1)"
                                    class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-xl font-bold">−</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                    max="{{ $variant->stock_quantity }}"
                                    class="w-20 text-center border rounded-lg py-2">
                                <button type="button" onclick="changeQty(1)"
                                    class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-xl font-bold">+</button>
                                <span class="text-gray-600 ml-2">({{ $variant->stock_quantity }} available)</span>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        <div class="flex gap-3 pb-3 border-b mb-3">
                            @php
                                $image =
                                    $variant->images->where('is_primary', true)->first() ?? $variant->images->first();
                            @endphp
                            <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden">
                                <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/64' }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $variant->name ?? 'Default' }}</p>
                                <p class="text-orange-600 font-bold">Rs. {{ number_format($price, 2) }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold" id="subtotalDisplay">Rs.
                                    {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span id="shippingDisplay">
                                    @if ($shipping > 0)
                                        Rs. {{ number_format($shipping, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-orange-600" id="totalDisplay">Rs.
                                        {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" form="directOrderForm"
                            class="w-full mt-6 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700 font-semibold">
                            Proceed to Payment
                        </button>

                        <a href="{{ route('products.show', $product->slug) }}"
                            class="block text-center mt-3 text-gray-500 hover:text-gray-700 text-sm">
                            ← Back to Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Modal -->
    <div id="addAddressModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-5 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Add New Address</h3>
                    <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto px-5 py-3">
                    <div id="modalErrorMessage" class="mb-3 text-red-600 text-sm hidden"></div>
                    <form id="newAddressForm" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone_no" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                            <select name="province_id" id="province_select" required
                                class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select Province</option>
                                @foreach (\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                            <select name="district_id" id="district_select" required
                                class="w-full px-3 py-2 border rounded-lg" disabled>
                                <option value="">First select province</option>
                            </select>
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                            <input type="text" name="address_line1" required
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="address_line2" class="w-full px-3 py-2 border rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nearest Landmark</label>
                            <input type="text" name="nearest_landmark" class="w-full px-3 py-2 border rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 p-5 border-t flex-shrink-0">
                    <button type="button"
                        class="close-modal px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" form="newAddressForm"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Addresses Modal -->
    <div id="manageAddressesModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center p-5 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Manage Addresses</h3>
                    <button type="button" class="close-manage-modal text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="manageAddressesList" class="flex-1 overflow-y-auto p-5 space-y-3">
                    <div class="text-center text-gray-500 py-4">Loading...</div>
                </div>
                <div class="p-5 border-t flex-shrink-0">
                    <div class="flex justify-end">
                        <button type="button"
                            class="close-manage-modal px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div id="editAddressModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-5 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Edit Address</h3>
                    <button type="button" class="close-edit-modal text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto px-5 py-3">
                    <div id="editModalErrorMessage" class="mb-3 text-red-600 text-sm hidden"></div>
                    <form id="editAddressForm" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="address_id" id="edit_address_id">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone_no" id="edit_phone_no" required
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="edit_email"
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                            <select name="province_id" id="edit_province_id" required
                                class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select Province</option>
                                @foreach (\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                            <select name="district_id" id="edit_district_id" required
                                class="w-full px-3 py-2 border rounded-lg" disabled>
                                <option value="">First select province</option>
                            </select>
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                            <input type="text" name="address_line1" id="edit_address_line1" required
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="address_line2" id="edit_address_line2"
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nearest Landmark</label>
                            <input type="text" name="nearest_landmark" id="edit_nearest_landmark"
                                class="w-full px-3 py-2 border rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 p-5 border-t flex-shrink-0">
                    <button type="button"
                        class="close-edit-modal px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" form="editAddressForm"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal (Delete) -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full">
                <div class="p-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Delete Address</h3>
                    <p class="text-gray-500">Are you sure you want to delete this address? This action cannot be
                        undone.</p>
                    <div class="flex justify-center gap-3 mt-6">
                        <button type="button" id="confirmCancelBtn"
                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="button" id="confirmDeleteBtn"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== Quantity and price calculation ====================
        const pricePerUnit = {{ $price }};
        const shippingFreeThreshold = 2000;
        const baseShipping = 100;

        function updateTotals() {
            let qty = parseInt(document.getElementById('quantity').value) || 1;
            let subtotal = pricePerUnit * qty;
            let shipping = subtotal > shippingFreeThreshold ? 0 : baseShipping;
            let total = subtotal + shipping;

            document.getElementById('subtotalDisplay').innerText = 'Rs. ' + subtotal.toFixed(2);
            document.getElementById('shippingDisplay').innerHTML = shipping ? 'Rs. ' + shipping.toFixed(2) : 'Free';
            document.getElementById('totalDisplay').innerText = 'Rs. ' + total.toFixed(2);
        }

        function changeQty(delta) {
            let input = document.getElementById('quantity');
            let newVal = parseInt(input.value) + delta;
            let max = parseInt(input.max);
            if (newVal >= 1 && newVal <= max) {
                input.value = newVal;
                updateTotals();
            }
        }

        document.getElementById('quantity').addEventListener('input', updateTotals);

        // ==================== CSRF token helper ====================
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value;
        }

        // ==================== Helper: Refresh main address dropdown ====================
        async function refreshAddressDropdown(selectedId = null) {
            try {
                const res = await fetch('/addresses', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error();
                const addresses = await res.json();
                const select = document.getElementById('address_id');
                const currentSelected = selectedId !== null ? selectedId : select.value;
                select.innerHTML = '<option value="">Select an address</option>';
                addresses.forEach(addr => {
                    const option = document.createElement('option');
                    option.value = addr.id;
                    option.textContent =
                        `${addr.address_line1}${addr.address_line2 ? ', '+addr.address_line2 : ''}, ${addr.district?.name || ''}, ${addr.province?.name || ''}${addr.nearest_landmark ? ' (Near: '+addr.nearest_landmark+')' : ''}`;
                    if (addr.id == currentSelected) option.selected = true;
                    select.appendChild(option);
                });
            } catch (err) {
                console.error('Failed to refresh addresses', err);
            }
        }

        // ==================== Load districts for a province ====================
        async function loadDistricts(provinceId, districtSelectId, selectedDistrictId = null) {
            const districtSelect = document.getElementById(districtSelectId);
            if (!provinceId) {
                districtSelect.innerHTML = '<option value="">First select province</option>';
                districtSelect.disabled = true;
                return;
            }
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Loading...</option>';
            try {
                const res = await fetch(`/districts/by-province/${provinceId}`);
                if (!res.ok) throw new Error();
                const districts = await res.json();
                districtSelect.innerHTML = '<option value="">Select District</option>';
                districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    if (selectedDistrictId && district.id == selectedDistrictId) option.selected = true;
                    districtSelect.appendChild(option);
                });
                districtSelect.disabled = false;
            } catch (err) {
                districtSelect.innerHTML = '<option value="">Error loading districts</option>';
                console.error(err);
            }
        }

        // ==================== Add Address Modal ====================
        const addModal = document.getElementById('addAddressModal');
        const openAddBtn = document.getElementById('openAddressModalBtn');
        const closeAddBtns = document.querySelectorAll('#addAddressModal .close-modal');
        const addForm = document.getElementById('newAddressForm');
        const modalErrorMessage = document.getElementById('modalErrorMessage');

        function resetAddForm() {
            addForm.reset();
            document.querySelectorAll('#newAddressForm .error-message').forEach(el => el.classList.add('hidden'));
            modalErrorMessage.classList.add('hidden');
            loadDistricts(null, 'district_select');
        }

        openAddBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            resetAddForm();
            addModal.classList.remove('hidden');
        });

        closeAddBtns.forEach(btn => btn.addEventListener('click', () => addModal.classList.add('hidden')));

        // Province -> District chaining for Add modal
        const addProvinceSelect = document.getElementById('province_select');
        const addDistrictSelect = document.getElementById('district_select');
        addProvinceSelect?.addEventListener('change', (e) => {
            loadDistricts(e.target.value, 'district_select');
        });

        // Submit new address
        addForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            // Clear previous errors
            document.querySelectorAll('#newAddressForm .error-message').forEach(el => el.classList.add(
                'hidden'));
            modalErrorMessage.classList.add('hidden');

            const formData = new FormData(addForm);
            const res = await fetch('/addresses', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (res.ok) {
                const newAddress = await res.json();
                await refreshAddressDropdown(newAddress.id);
                addModal.classList.add('hidden');
                // Show success message
                const successDiv = document.getElementById('addressSuccessMessage');
                if (successDiv) {
                    successDiv.textContent = 'Address added successfully!';
                    successDiv.classList.remove('hidden');
                    setTimeout(() => successDiv.classList.add('hidden'), 3000);
                }
            } else {
                const errorData = await res.json();
                if (errorData.errors) {
                    for (const [field, messages] of Object.entries(errorData.errors)) {
                        const errorSpan = document.querySelector(`#newAddressForm [name="${field}"]`)?.closest(
                            'div')?.querySelector('.error-message');
                        if (errorSpan) {
                            errorSpan.textContent = messages[0];
                            errorSpan.classList.remove('hidden');
                        }
                    }
                } else {
                    modalErrorMessage.textContent = errorData.message || 'Something went wrong';
                    modalErrorMessage.classList.remove('hidden');
                }
            }
        });

        // ==================== Manage Addresses Modal ====================
        const manageModal = document.getElementById('manageAddressesModal');
        const manageBtn = document.getElementById('manageAddressesBtn');
        const closeManageBtns = document.querySelectorAll('#manageAddressesModal .close-manage-modal');
        const manageList = document.getElementById('manageAddressesList');
        let currentDeleteId = null;

        async function loadManageAddresses() {
            manageList.innerHTML = '<div class="text-center text-gray-500 py-4">Loading...</div>';
            try {
                const res = await fetch('/addresses', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error();
                const addresses = await res.json();
                if (addresses.length === 0) {
                    manageList.innerHTML =
                        '<div class="text-center text-gray-500 py-4">No addresses found. Add one using the "Add New Address" button.</div>';
                    return;
                }
                manageList.innerHTML = '';
                addresses.forEach(addr => {
                    const card = document.createElement('div');
                    card.className = 'border rounded-lg p-4 bg-gray-50';
                    card.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">${escapeHtml(addr.name)}</p>
                                <p class="text-sm text-gray-600">${escapeHtml(addr.phone_no)}</p>
                                <p class="text-sm text-gray-600">${escapeHtml(addr.email || '')}</p>
                                <p class="text-sm">${escapeHtml(addr.address_line1)}${addr.address_line2 ? ', '+escapeHtml(addr.address_line2) : ''}</p>
                                <p class="text-sm">${addr.district?.name || ''}, ${addr.province?.name || ''}</p>
                                ${addr.nearest_landmark ? `<p class="text-sm text-gray-500">Near: ${escapeHtml(addr.nearest_landmark)}</p>` : ''}
                            </div>
                            <div class="flex gap-2">
                                <button class="edit-address-btn text-blue-600 hover:text-blue-800 text-sm" data-id="${addr.id}">Edit</button>
                                <button class="delete-address-btn text-red-600 hover:text-red-800 text-sm" data-id="${addr.id}">Delete</button>
                            </div>
                        </div>
                    `;
                    manageList.appendChild(card);
                });
                // Attach edit/delete events
                document.querySelectorAll('.edit-address-btn').forEach(btn => {
                    btn.addEventListener('click', () => openEditModal(btn.dataset.id));
                });
                document.querySelectorAll('.delete-address-btn').forEach(btn => {
                    btn.addEventListener('click', () => openConfirmModal(btn.dataset.id));
                });
            } catch (err) {
                manageList.innerHTML = '<div class="text-center text-red-500 py-4">Failed to load addresses.</div>';
            }
        }

        function openConfirmModal(addressId) {
            currentDeleteId = addressId;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
            if (!currentDeleteId) return;
            try {
                const res = await fetch(`/addresses/${currentDeleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    await refreshAddressDropdown();
                    if (manageModal.classList.contains('hidden') === false) await loadManageAddresses();
                    document.getElementById('confirmModal').classList.add('hidden');
                    // If deleted address was selected, clear selection
                    const addressSelect = document.getElementById('address_id');
                    if (addressSelect.value == currentDeleteId) addressSelect.value = '';
                    currentDeleteId = null;
                    // Show success message
                    const successDiv = document.getElementById('addressSuccessMessage');
                    if (successDiv) {
                        successDiv.textContent = 'Address deleted successfully!';
                        successDiv.classList.remove('hidden');
                        setTimeout(() => successDiv.classList.add('hidden'), 3000);
                    }
                } else {
                    alert('Failed to delete address');
                }
            } catch (err) {
                alert('Error deleting address');
            }
        });

        document.getElementById('confirmCancelBtn')?.addEventListener('click', () => {
            document.getElementById('confirmModal').classList.add('hidden');
            currentDeleteId = null;
        });

        manageBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            loadManageAddresses();
            manageModal.classList.remove('hidden');
        });

        closeManageBtns.forEach(btn => btn.addEventListener('click', () => manageModal.classList.add('hidden')));

        // ==================== Edit Address Modal ====================
        const editModal = document.getElementById('editAddressModal');
        const closeEditBtns = document.querySelectorAll('#editAddressModal .close-edit-modal');
        const editForm = document.getElementById('editAddressForm');
        const editErrorMessage = document.getElementById('editModalErrorMessage');

        function resetEditForm() {
            editForm.reset();
            document.querySelectorAll('#editAddressForm .edit-error-message').forEach(el => el.classList.add('hidden'));
            editErrorMessage.classList.add('hidden');
            loadDistricts(null, 'edit_district_id');
        }

        async function openEditModal(addressId) {
            resetEditForm();
            editModal.classList.remove('hidden');
            try {
                const res = await fetch(`/addresses/${addressId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error();
                const addr = await res.json();
                document.getElementById('edit_address_id').value = addr.id;
                document.getElementById('edit_name').value = addr.name;
                document.getElementById('edit_phone_no').value = addr.phone_no;
                document.getElementById('edit_email').value = addr.email || '';
                document.getElementById('edit_address_line1').value = addr.address_line1;
                document.getElementById('edit_address_line2').value = addr.address_line2 || '';
                document.getElementById('edit_nearest_landmark').value = addr.nearest_landmark || '';
                // Set province and load districts
                const provinceSelect = document.getElementById('edit_province_id');
                provinceSelect.value = addr.province_id;
                await loadDistricts(addr.province_id, 'edit_district_id', addr.district_id);
            } catch (err) {
                editErrorMessage.textContent = 'Failed to load address details';
                editErrorMessage.classList.remove('hidden');
            }
        }

        // Province -> District for edit modal
        const editProvinceSelect = document.getElementById('edit_province_id');
        editProvinceSelect?.addEventListener('change', (e) => {
            loadDistricts(e.target.value, 'edit_district_id');
        });

        editForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            document.querySelectorAll('#editAddressForm .edit-error-message').forEach(el => el.classList.add(
                'hidden'));
            editErrorMessage.classList.add('hidden');

            const addressId = document.getElementById('edit_address_id').value;
            const formData = new FormData(editForm);
            // Override method to PUT
            formData.append('_method', 'PUT');

            const res = await fetch(`/addresses/${addressId}`, {
                method: 'POST', // because we use _method=PUT
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (res.ok) {
                await refreshAddressDropdown(addressId);
                editModal.classList.add('hidden');
                if (manageModal.classList.contains('hidden') === false) await loadManageAddresses();
                // Show success message
                const successDiv = document.getElementById('addressSuccessMessage');
                if (successDiv) {
                    successDiv.textContent = 'Address updated successfully!';
                    successDiv.classList.remove('hidden');
                    setTimeout(() => successDiv.classList.add('hidden'), 3000);
                }
            } else {
                const errorData = await res.json();
                if (errorData.errors) {
                    for (const [field, messages] of Object.entries(errorData.errors)) {
                        const errorSpan = document.querySelector(`#editAddressForm [name="${field}"]`)?.closest(
                            'div')?.querySelector('.edit-error-message');
                        if (errorSpan) {
                            errorSpan.textContent = messages[0];
                            errorSpan.classList.remove('hidden');
                        }
                    }
                } else {
                    editErrorMessage.textContent = errorData.message || 'Update failed';
                    editErrorMessage.classList.remove('hidden');
                }
            }
        });

        closeEditBtns.forEach(btn => btn.addEventListener('click', () => editModal.classList.add('hidden')));

        // ==================== Helper to escape HTML ====================
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Close modals when clicking outside (optional)
        window.addEventListener('click', (e) => {
            if (e.target === addModal) addModal.classList.add('hidden');
            if (e.target === manageModal) manageModal.classList.add('hidden');
            if (e.target === editModal) editModal.classList.add('hidden');
            if (e.target === document.getElementById('confirmModal')) document.getElementById('confirmModal')
                .classList.add('hidden');
        });
    </script>
</x-frontend.layout>
