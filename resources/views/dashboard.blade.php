<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(auth()->user()->role === 'employee')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Salary Request Box -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Salary Request</h2>
                        <p class="text-gray-600 mb-4">Submit your monthly salary request here.</p>
                        <a href="{{ route('salary-requests.index') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Make Request
                        </a>
                    </div>
                </div>

                <!-- Leave Request Box -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Leave Request</h2>
                        <p class="text-gray-600 mb-4">Submit your leave request here.</p>
                        <a href="{{ route('leave-requests.index') }}"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Request Leave
                        </a>
                    </div>
                </div>
            </div>

            <!-- Calendar for employee -->
            @include('partials.employee-calendar')

        @elseif(auth()->user()->role === 'department_admin')
            <!-- Department Admin Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Employee Management Box -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Employee Management</h2>
                        <div class="space-y-4">
                            <a href="{{ route('department.employees.index') }}"
                               class="block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                                Manage Employees
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Schedule Management Box -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Schedule Management</h2>
                        <div class="space-y-4">
                            <a href="{{ route('department.schedules.index') }}"
                               class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                Manage Work Schedules
                            </a>
                            <a href="{{ route('department.assets.index') }}"
                               class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                Manage Assets
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Requests Management Box -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Requests Management</h2>
                        <div class="space-y-4">
                            <a href="{{ route('department.salary-requests') }}"
                               class="block bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                                View Salary Requests
                            </a>
                            <a href="{{ route('department.leave-requests') }}"
                               class="block bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">
                                View Leave Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Super Admin Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">User Management</h2>
                        <a href="{{ route('users.index') }}"
                           class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                            Manage Users
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Department Management</h2>
                        <a href="{{ route('departments.index') }}"
                           class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                            Manage Departments
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Asset Management</h2>
                        <a href="{{ route('super-admin.assets.index') }}"
                           class="block bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">
                            Manage Assets
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Reports</h2>
                        <div class="space-y-2">
                            <a href="{{ route('reports.salary-requests') }}"
                               class="block bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                                Salary Reports
                            </a>
                            <a href="{{ route('reports.leave-requests') }}"
                               class="block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                                Leave Reports
                            </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(auth()->user()->role === 'employee')
        @push('scripts')
            @include('partials.calendar-scripts')
        @endpush
    @endif
</x-layouts.app>
