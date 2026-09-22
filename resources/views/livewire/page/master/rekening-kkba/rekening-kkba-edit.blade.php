<div>
    <h1 class="mb-4 text-2xl font-bold md:text-3xl text-slate-800">{{ $titlePage }}</h1>
    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <div class="px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200">
            <x-elements.button wire:navigate :href="route('master.rekening-kkba.show', $id)" :variant="'primary'" :style="'outlined'" :type="'link'">
                <x-lucide-arrow-left class="size-5" />
                <span>Kembali ke detail</span>
            </x-elements.button>
        </div>

        @include('livewire.page.master.rekening-kkba._form', [
            'submitMethod' => 'saveUpdate',
            'submitLabel' => 'Perbarui data',
        ])
    </div>
</div>
