<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Company Assets Management</h1>
            <a href="{{ route('super-admin.assets.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Asset
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form action="{{ route('super-admin.assets.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <select name="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asset Type</label>
                        <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">All Types</option>
                            <option value="computer" {{ request('type') == 'computer' ? 'selected' : '' }}>Computer</option>
                            <option value="phone" {{ request('type') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Assignment Status</label>
                        <select name="assigned" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">All Status</option>
                            <option value="1" {{ request('assigned') === '1' ? 'selected' : '' }}>Assigned</option>
                            <option value="0" {{ request('assigned') === '0' ? 'selected' : '' }}>Unassigned</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Assets Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($assets as $asset)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($asset->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->department->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($asset->assignedUser)
                                {{ $asset->assignedUser->name }}
                                <br>
                                <span class="text-xs text-gray-500">
                                        Since: {{ $asset->assigned_date->format('Y-m-d') }}
                                    </span>
                            @else
                                <span class="text-gray-500">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $asset->serial_number ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('super-admin.assets.edit', $asset) }}"
                                   class="text-blue-600 hover:text-blue-900">Edit</a>
                                <button onclick="deleteAsset({{ $asset->id }})"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $assets->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function deleteAsset(assetId) {
                if (confirm('Are you sure you want to delete this asset? This action cannot be undone.')) {
                    fetch(`/super-admin/assets/${assetId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                throw new Error(data.error);
                            }
                            window.location.reload();
                        })
                        .catch(error => {
                            alert('Error deleting asset: ' + error.message);
                        });
                }
            }
        </script>
    @endpush
</x-layouts.app>
