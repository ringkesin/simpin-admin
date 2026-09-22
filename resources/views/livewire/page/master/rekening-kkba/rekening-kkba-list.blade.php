<div>
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">{{ $titlePage }}</h1>
        <div class="flex justify-end">
            <x-elements.button wire:navigate :href="route('master.rekening-kkba.create')" :type="'link'">
                <x-lucide-plus class="size-4" />
                <span>Tambah Rekening</span>
            </x-elements.button>
        </div>
    </div>

    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <livewire:page.master.rekening-kkba.rekening-kkba-table />
    </div>
</div>
