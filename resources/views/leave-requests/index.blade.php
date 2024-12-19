<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Leave Requests</h1>
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
                        <th class="text-left">Leave Type</th>
                        <th class="text-left">Start Date</th>
                        <th class="text-left">End Date</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaveRequests as $request)
                        <tr class="border-t">
                            <td class="py-3">{{ ucfirst($request->leave_type) }}</td>
                            <td>{{ $request->start_date->format('Y-m-d') }}</td>
                            <td>{{ $request->end_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
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
                <h2 class="text-xl font-semibold mb-4" id="modalTitle">New Leave Request</h2>
                <form id="leaveRequestForm" method="POST" action="{{ route('leave-requests.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="leave_type">
                            Leave Type
                        </label>
                        <select name="leave_type" id="leave_type" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="annual">Annual Leave</option>
                            <option value="sick">Sick Leave</option>
                            <option value="unpaid">Unpaid Leave</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">
                            Start Date
                        </label>
                        <input type="date" name="start_date" id="start_date" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">
                            End Date
                        </label>
                        <input type="date" name="end_date" id="end_date" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description (Optional)
                        </label>
                        <textarea name="description" id="description"
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                  rows="3"></textarea>
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
            function toggleModal() {
                const modal = document.getElementById('requestModal');
                if (modal.style.display === "none") {
                    modal.style.display = "block";
                } else {
                    modal.style.display = "none";
                    document.getElementById('leaveRequestForm').reset();
                }
            }

            function editRequest(requestId) {
                // Implement edit functionality
                console.log('Edit request:', requestId);
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('requestModal');
                if (event.target === modal) {
                    toggleModal();
                }
            }

            // Validate end date is after start date
            document.getElementById('start_date').addEventListener('change', function() {
                document.getElementById('end_date').min = this.value;
            });
        </script>
    @endpush
</x-layouts.app>
