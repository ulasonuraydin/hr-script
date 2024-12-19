<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('super-admin.assets.index') }}"
                   class="text-blue-600 hover:text-blue-900">← Back to Assets</a>
                <h1 class="text-2xl font-semibold">Add New Asset</h1>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form action="{{ route('super-admin.assets.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Asset Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="name">
                            Asset Name
                        </label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asset Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="type">
                            Asset Type
                        </label>
                        <select name="type" id="type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                required>
                            <option value="">Select Type</option>
                            <option value="computer" {{ old('type') == 'computer' ? 'selected' : '' }}>Computer</option>
                            <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="serial_number">
                            Serial Number
                        </label>
                        <input type="text" name="serial_number" id="serial_number"
                               value="{{ old('serial_number') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('serial_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="department_id">
                            Department
                        </label>
                        <select name="department_id" id="department_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- User Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="assigned_to">
                            Assign To User
                        </label>
                        <select name="assigned_to" id="assigned_to"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Not Assigned</option>
                        </select>
                        @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assignment Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="assigned_date">
                            Assignment Date
                        </label>
                        <input type="date" name="assigned_date" id="assigned_date"
                               value="{{ old('assigned_date') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('assigned_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="description">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('super-admin.assets.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Asset
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle department change to load users
            document.getElementById('department_id').addEventListener('change', function() {
                const departmentId = this.value;
                const userSelect = document.getElementById('assigned_to');

                // Clear current options
                userSelect.innerHTML = '<option value="">Not Assigned</option>';

                if (!departmentId) return;

                // Fetch users for selected department
                fetch(`/super-admin/department/${departmentId}/users`)
                    .then(response => response.json())
                    .then(users => {
                        users.forEach(user => {
                            const option = new Option(user.name, user.id);
                            userSelect.add(option);
                        });
                    })
                    .catch(error => console.error('Error loading users:', error));
            });

            // Show/hide assignment date based on user assignment
            document.getElementById('assigned_to').addEventListener('change', function() {
                const dateInput = document.getElementById('assigned_date');
                if (this.value) {
                    dateInput.required = true;
                    dateInput.value = new Date().toISOString().split('T')[0];
                } else {
                    dateInput.required = false;
                    dateInput.value = '';
                }
            });
        </script>
    @endpush
</x-layouts.app>
