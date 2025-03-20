<div>
    {{-- Do your work, then step back. --}}
    <!-- Title & Actions Button -->
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
        <div>
            <div class="flex justify-end">
                <div>
                    <x-elements.button wire:navigate :href="route('user.create')" button-type="{{ 'primary' }}" :type="'link'">
                        <x-lucide-plus class="size-3"/>
                        <span class="xs:block">Tambah </span>
                    </x-elements.button>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <livewire:page.user.users-table />
    </div>
</div>
