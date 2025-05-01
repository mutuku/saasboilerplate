<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ $editingTenantId ? 'Edit Tenant' : 'Create New Tenant' }}
        </h3>

        <form wire:submit="{{ $editingTenantId ? 'updateTenant' : 'createTenant' }}">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="identifier" class="block text-sm font-medium text-gray-700">Identifier</label>
                <input type="text" id="identifier" wire:model="identifier" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('identifier') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                @if ($editingTenantId)
                    <button type="button" wire:click="cancelEdit" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded-md">
                        Cancel
                    </button>
                @endif

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                    {{ $editingTenantId ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Identifier</th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($tenants as $tenant)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->identifier }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/{{ $tenant->identifier }}/dashboard" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            <button wire:click="editTenant({{ $tenant->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</button>
                            <button wire:click="deleteTenant({{ $tenant->id }})" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
