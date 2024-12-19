<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Employee Profile</h1>
                <div class="space-x-3">
                    <a href="{{ route('department.employees.edit', $employee) }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Profile
                    </a>
                    <a href="{{ route('department.employees.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Full Name</p>
                    <p class="font-medium">{{ $employee->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium">{{ $employee->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Phone</p>
                    <p class="font-medium">{{ $employee->phone ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Joining Date</p>
                    <p class="font-medium">{{ $employee->joining_date ? $employee->joining_date->format('Y-m-d') : 'Not set' }}</p>
                </div>
                @if($employee->address)
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="font-medium">{{ $employee->address }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Emergency Contact</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Contact Name</p>
                    <p class="font-medium">{{ $employee->emergency_contact ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Contact Phone</p>
                    <p class="font-medium">{{ $employee->emergency_phone ?? 'Not set' }}</p>
                </div>
            </div>
        </div>

        <!-- Work Schedule -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Current Work Schedule</h2>
            @if($employee->workSchedules->isNotEmpty())
                <div class="space-y-4">
                    @foreach($employee->workSchedules->take(1) as $schedule)
                        <div class="border rounded p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Shift Name</p>
                                    <p class="font-medium">{{ $schedule->shift_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Working Hours</p>
                                    <p class="font-medium">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Working Days</p>
                                    <p class="font-medium">
                                        @foreach($schedule->working_days as $day)
                                            {{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] }}
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No current work schedule assigned.</p>
            @endif
        </div>

        <!-- Assigned Assets -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Assigned Assets</h2>
            @if($employee->assignedAssets->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Date</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach($employee->assignedAssets as $asset)
                            <tr>
                                <td class="px-6 py-4">{{ $asset->name }}</td>
                                <td class="px-6 py-4">{{ ucfirst($asset->type) }}</td>
                                <td class="px-6 py-4">{{ $asset->serial_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $asset->assigned_date->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No assets currently assigned.</p>
            @endif
        </div>

        <!-- Recent Requests -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Leave Requests -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Recent Leave Requests</h2>
                @if($employee->leaveRequests->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($employee->leaveRequests->take(5) as $request)
                            <div class="border rounded p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ ucfirst($request->leave_type) }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $request->start_date->format('Y-m-d') }} to {{ $request->end_date->format('Y-m-d') }}
                                        </p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No recent leave requests.</p>
                @endif
            </div>

            <!-- Salary Requests -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Recent Salary Requests</h2>
                @if($employee->salaryRequests->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($employee->salaryRequests->take(5) as $request)
                            <div class="border rounded p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ $request->work_name }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $request->payment_method)) }}
                                        </p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $request->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No recent salary requests.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
