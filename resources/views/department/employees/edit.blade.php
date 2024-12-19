<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Edit Employee Profile</h1>
                <a href="{{ route('department.employees.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form action="{{ route('department.employees.update', $employee) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="name">
                            Full Name
                        </label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $employee->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="email">
                            Email Address
                        </label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $employee->email) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="password">
                            New Password (leave blank to keep current)
                        </label>
                        <input type="password" name="password" id="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="password_confirmation">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="phone">
                            Phone Number
                        </label>
                        <input type="text" name="phone" id="phone"
                               value="{{ old('phone', $employee->phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Joining Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="joining_date">
                            Joining Date
                        </label>
                        <input type="date" name="joining_date" id="joining_date"
                               value="{{ old('joining_date', $employee->joining_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('joining_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="address">
                        Address
                    </label>
                    <textarea name="address" id="address" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('address', $employee->address) }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Emergency Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="emergency_contact">
                            Emergency Contact Name
                        </label>
                        <input type="text" name="emergency_contact" id="emergency_contact"
                               value="{{ old('emergency_contact', $employee->emergency_contact) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('emergency_contact')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="emergency_phone">
                            Emergency Contact Phone
                        </label>
                        <input type="text" name="emergency_phone" id="emergency_phone"
                               value="{{ old('emergency_phone', $employee->emergency_phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('emergency_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="notes">
                        Additional Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $employee->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('department.employees.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
