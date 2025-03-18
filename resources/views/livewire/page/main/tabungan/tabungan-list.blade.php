<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
        <div>
            <div class="flex justify-end gap-3">
                <div>
                    <x-elements.button wire:navigate :href="route('main.tabungan.create')" button-type="{{ 'primary' }}" :type="'link'">
                        <x-lucide-plus class="size-3"/>
                        <span class="xs:block">Tambah </span>
                    </x-elements.button>
                </div>
                <div>
                    <x-elements.button wire:navigate :href="route('main.tabungan.export')" button-type="{{ 'primary' }}" :type="'link'">
                        <x-lucide-folder-input class="size-3"/>
                        <span class="xs:block">Export </span>
                    </x-elements.button>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <livewire:page.main.tabungan.tabungan-table />
    </div>
</div>
