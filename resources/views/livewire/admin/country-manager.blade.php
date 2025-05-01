<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-900">Country Management</h3>
        <p class="text-gray-600 mt-1">Manage countries in the system</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div x-data="{ activeTab: 'list' }"
             x-on:switch-to-list-tab.window="activeTab = 'list'"
             x-on:switch-to-form-tab.window="activeTab = 'form'">
            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'list'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'list', 'border-transparent': activeTab !== 'list'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Country List
                    </button>
                    <button @click="activeTab = 'form'; $wire.resetForm()"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'form', 'border-transparent': activeTab !== 'form'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        {{ $isEditMode ? 'Edit Country' : 'Create Country' }}
                    </button>
                </nav>
            </div>

            {{-- Country List Tab --}}
            <div x-show="activeTab === 'list'">
                {{-- Search + Create Button --}}
                <div class="mb-4 flex justify-between items-center">
                    <input wire:model.live.debounce.300ms="searchTerm" placeholder="Search countries..."
                           class="w-1/3 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-500" />
                    <button wire:click="resetForm" @click="activeTab = 'form'"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                        Create New Country
                    </button>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($this->countries as $country)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end space-x-2">
                                            <button wire:click="edit({{ $country->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete({{ $country->id }})"
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-gray-500">No countries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $this->countries->links() }}
                </div>
            </div>

            {{-- Country Form Tab --}}
            <div x-show="activeTab === 'form'">
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                            <input type="text" id="code" wire:model="code"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" wire:model="name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="resetForm" @click="activeTab = 'list'"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ open: @entangle('confirmingDeletion') }"
         x-show="open"
         x-cloak
         class="fixed z-10 inset-0 overflow-y-auto"
         role="dialog"
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open"
                 x-transition.opacity.duration.300ms
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 aria-hidden="true"></div>
            <div x-show="open"
                 x-transition.scale.duration.300ms
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Country</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to delete this country? This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            wire:click="delete"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto">
                        Delete
                    </button>
                    <button type="button"
                            wire:click="$set('confirmingDeletion', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
