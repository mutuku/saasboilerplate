<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative flex-1 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <livewire:dashboard/>
        </div>

        @if(auth()->user()->is_super_admin || auth()->user()->is_tenant_admin)
        <div class="relative flex-1 rounded-xl border border-neutral-200 dark:border-neutral-700  py-6 px-4">
                <livewire:user-manager/>
        </div>
        @endif
    </div>
</x-layouts.app>
