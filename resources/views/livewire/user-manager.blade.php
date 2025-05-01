<div>
    {{-- Notification messages --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-show="show"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => { show = false }, 3000)"
        :class="type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'"
        class="border px-4 py-3 rounded mb-4 transition-all"
        style="display: none;"
    >
        <div class="flex">
            <div class="py-1">
                <template x-if="type === 'success'">
                    <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM10 9.41l2.12-2.12 1.41 1.41L11.41 10l2.12 2.12-1.41 1.41L10 11.41l-2.12 2.12-1.41-1.41L8.59 10 6.47 7.88l1.41-1.41L10 8.59z"/>
                    </svg>
                </template>
            </div>
            <div>
                <p x-text="message"></p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-900">User Management</h3>
        <p class="text-gray-600 mt-1">Manage your organization's users and their permissions</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        {{-- User Management Tabs --}}
        <div x-data="{ activeTab: 'list' }"  x-on:switch-to-list-tab.window="activeTab = 'list'">
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex -mb-px space-x-8" aria-label="Tabs">
                    <button
                        @click="activeTab = 'list'"
                        :class="activeTab === 'list' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        User List
                    </button>
                    <button
                        @click="activeTab = 'form'; $wire.resetForm()"
                        :class="activeTab === 'form' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        {{ $isEditMode ? 'Edit User' : 'Create User' }}
                    </button>
                </nav>
            </div>

            {{-- User List Tab --}}
            <div x-show="activeTab === 'list'">
                {{-- Search and Filter --}}
                <div class="mb-4 flex justify-between items-center">
                    <div class="w-1/3">
                        <div class="relative rounded-md shadow-sm">
                            <input
                                type="text"
                                wire:model.debounce.300ms="searchTerm"
                                placeholder="Search users..."
                                class="form-input block w-full pr-10 sm:text-sm sm:leading-5 rounded-md border-gray-300"
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->is_super_admin || auth()->user()->is_tenant_admin)
                    <button
                        wire:click="resetForm"
                        @click="activeTab = 'form'"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition"
                    >
                        Create New User
                    </button>
                    @endif
                </div>

                {{-- User Table --}}
                <div class="overflow-x-auto bg-white rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                            <tr class="
                                {{ $user->deactivated ? 'bg-gray-50' : '' }}
                                {{ $user->id === $recentlyModifiedUserId ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}
                            ">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">{{ $user->initials() }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->tenant->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($user->is_super_admin)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Super Admin
                                            </span>
                                        @elseif($user->is_tenant_admin)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Tenant Admin
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($user->deactivated)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button
                                                wire:click="edit({{ $user->id }})"
                                                @click="activeTab = 'form'"
                                                class="text-blue-600 hover:text-blue-900"
                                                {{ !$user->is_super_admin || Auth::user()->is_super_admin ? '' : 'disabled' }}
                                                title="Edit user"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </button>

                                            @if((!$user->is_super_admin && $user->id !== Auth::id()) || Auth::user()->is_super_admin)
                                                <button
                                                    wire:click="confirmToggleActivation({{ $user->id }})"
                                                    class="{{ $user->deactivated ? 'text-green-600 hover:text-green-900' : 'text-yellow-600 hover:text-yellow-900' }}"
                                                    title="{{ $user->deactivated ? 'Activate user' : 'Deactivate user' }}"
                                                >
                                                    @if($user->deactivated)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </button>

                                                <button
                                                    wire:click="confirmDelete({{ $user->id }})"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete user"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No users found.
                                        @if(!empty($searchTerm))
                                            Try adjusting your search.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>

            {{-- User Form Tab --}}
            <div x-show="activeTab === 'form'">
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'create' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input
                                type="text"
                                id="name"
                                wire:model="name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                wire:model="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tenant Selection (Super Admin only) --}}
                        @if(Auth::user()->is_super_admin)
                            <div class="col-span-2 md:col-span-1">
                                <label for="tenant_id" class="block text-sm font-medium text-gray-700">Tenant</label>
                                <select
                                    id="tenant_id"
                                    wire:model="tenant_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                >
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Admin Role Options (Super Admin only) --}}
                            <div class="col-span-2 md:col-span-1">
                                <fieldset class="mt-7">
                                    <legend class="text-sm font-medium text-gray-700">User Role</legend>
                                    <div class="mt-2 space-y-3">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="is_super_admin"
                                                    wire:model="is_super_admin"
                                                    type="checkbox"
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                >
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="is_super_admin" class="font-medium text-gray-700">Super Admin</label>
                                                <p class="text-gray-500">Full access to all features and tenants</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="is_tenant_admin"
                                                    wire:model="is_tenant_admin"
                                                    type="checkbox"
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                >
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="is_tenant_admin" class="font-medium text-gray-700">Tenant Admin</label>
                                                <p class="text-gray-500">Admin access for a specific tenant</p>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        @endif

                        {{-- Permission Selection --}}
                        @if(Auth::user()->is_tenant_admin || Auth::user()->is_super_admin)
                            <div class="col-span-2">
                                <fieldset>
                                    <legend class="text-base font-medium text-gray-700 mb-4">User Permissions</legend>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach ($allPermissions as $group => $permissions)
                                            <div class="border p-4 rounded-lg shadow-sm">
                                                <h4 class="text-md font-semibold mb-3 border-b pb-2">{{ $group }}</h4>

                                                <div class="space-y-2">
                                                    @foreach ($permissions as $permission)
                                                        <label class="flex items-center">
                                                            <input
                                                                type="checkbox"
                                                                wire:model="permissions"
                                                                value="{{ $permission->name }}"
                                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                            >
                                                            <span class="ml-2 capitalize text-sm">{{ str_replace('_', ' ', $permission->name) }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </fieldset>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                        <button
                            type="button"
                            wire:click="resetForm"
                            @click="activeTab = 'list'"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            {{ $isEditMode ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- Delete Confirmation Modal --}}
    <div
        x-data="{ open: @entangle('confirmingUserDeletion') }"
        x-show="open"
        x-cloak
        class="fixed z-10 inset-0 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Delete User
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this user? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        wire:click="delete"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Delete
                    </button>
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toggle Activation Confirmation Modal --}}
    <div
        x-data="{ open: @entangle('confirmingUserDeactivation') }"
        x-show="open"
        x-cloak
        class="fixed z-10 inset-0 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ $userToToggle && $userToToggle->deactivated ? 'Activate User' : 'Deactivate User' }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ $userToToggle && $userToToggle->deactivated
                                        ? 'Are you sure you want to activate this user? They will be able to log in again.'
                                        : 'Are you sure you want to deactivate this user? They will no longer be able to log in.'
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        wire:click="toggleActivation"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $userToToggle && $userToToggle->deactivated ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500' }} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        {{ $userToToggle && $userToToggle->deactivated ? 'Activate' : 'Deactivate' }}
                    </button>
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
