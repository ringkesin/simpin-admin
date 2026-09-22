<div>
    <h1 class="mb-4 text-2xl font-bold md:text-3xl text-slate-800">{{ $titlePage }}</h1>
    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <div class="flex justify-between gap-2 px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200">
            <x-elements.button wire:navigate :href="route('master.rekening-kkba.list')" :variant="'primary'" :style="'outlined'" :type="'link'">
                <x-lucide-arrow-left class="size-5" />
                <span>Kembali ke daftar</span>
            </x-elements.button>
            <div class="flex gap-2">
                <x-elements.button wire:navigate :href="route('master.rekening-kkba.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
                    <x-lucide-square-pen class="size-4" />
                    <span>Ubah</span>
                </x-elements.button>
                <x-elements.button type="button" variant="danger" style="outlined" wire:click="delete" wire:confirm="Hapus rekening KKBA ini?">
                    <x-lucide-trash-2 class="size-4" />
                    <span>Hapus</span>
                </x-elements.button>
            </div>
        </div>

        <x-elements.header-form>Informasi Rekening</x-elements.header-form>
        <x-elements.detail label="Nama Bank">{{ $rekening->nama_bank }}</x-elements.detail>
        <x-elements.detail label="Nomor Rekening">{{ $rekening->nomor_rekening }}</x-elements.detail>
        <x-elements.detail label="Atas Nama">{{ $rekening->atas_nama }}</x-elements.detail>
        <x-elements.detail label="Status">{{ $rekening->is_active ? 'Aktif' : 'Tidak Aktif' }}</x-elements.detail>
        <x-elements.detail label="Keterangan">{{ $rekening->keterangan ?: '-' }}</x-elements.detail>
    </div>
</div>
