<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Salary Requests</h1>
            <button type="button" onclick="toggleModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                New Request
            </button>
        </div>

        <!-- Requests List -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <table class="min-w-full">
                    <thead>
                    <tr>
                        <th class="text-left">Work Name</th>
                        <th class="text-left">Payment Method</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Created At</th>
                        <th class="text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($salaryRequests as $request)
                        <tr class="border-t">
                            <td class="py-3">{{ $request->work_name }}</td>
                            <td>{{ $request->payment_method }}</td>
                            <td>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $request->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if($request->status === 'pending')
                                    <button onclick="editRequest({{ $request->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                        Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="requestModal" style="display: none;" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h2 class="text-xl font-semibold mb-4" id="modalTitle">New Salary Request</h2>
                <form id="salaryRequestForm" method="POST" action="{{ route('salary-requests.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="work_name">
                            Work Name
                        </label>
                        <input type="text" name="work_name" id="work_name" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_method">
                            Payment Method
                        </label>
                        <select name="payment_method" id="payment_method" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="tether_trc">Tether TRC</option>
                        </select>
                    </div>

                    <div id="wallet_address_div" class="mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="wallet_address">
                            Tether TRC Wallet Address
                        </label>
                        <input type="text" name="wallet_address" id="wallet_address"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="toggleModal()"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Function to toggle modal visibility
            function toggleModal() {
                const modal = document.getElementById('requestModal');
                if (modal.style.display === "none") {
                    modal.style.display = "block";
                } else {
                    modal.style.display = "none";
                    document.getElementById('salaryRequestForm').reset();
                }
            }

            // Function to handle payment method change
            document.getElementById('payment_method').addEventListener('change', function() {
                const walletDiv = document.getElementById('wallet_address_div');
                const walletInput = document.getElementById('wallet_address');

                if (this.value === 'tether_trc') {
                    walletDiv.classList.remove('hidden');
                    walletInput.required = true;
                } else {
                    walletDiv.classList.add('hidden');
                    walletInput.required = false;
                    walletInput.value = '';
                }
            });

            function editRequest(requestId) {
                // We'll implement this later
                console.log('Edit request:', requestId);
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('requestModal');
                if (event.target === modal) {
                    toggleModal();
                }
            }
        </script>
    @endpush
</x-layouts.app>
