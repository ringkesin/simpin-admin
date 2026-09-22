<form wire:submit="{{ $submitMethod }}">
    <div class="grid grid-cols-12 gap-x-10">
        <div class="col-span-12 md:col-span-6">
            <div class="mb-4">
                <x-form.label for="nama_bank">Nama Bank <span class="text-red-500">*</span></x-form.label>
                <x-form.input class="w-full mt-1" id="nama_bank" name="nama_bank" wire:model="nama_bank" placeholder="Contoh: Bank Mandiri" />
            </div>

            <div class="mb-4">
                <x-form.label for="nomor_rekening">Nomor Rekening <span class="text-red-500">*</span></x-form.label>
                <x-form.input class="w-full mt-1" id="nomor_rekening" name="nomor_rekening" wire:model="nomor_rekening" inputmode="numeric" placeholder="Masukkan nomor rekening" />
            </div>

            <div class="mb-4">
                <x-form.label for="atas_nama">Atas Nama <span class="text-red-500">*</span></x-form.label>
                <x-form.input class="w-full mt-1" id="atas_nama" name="atas_nama" wire:model="atas_nama" placeholder="Nama pemilik rekening" />
            </div>
        </div>

        <div class="col-span-12 md:col-span-6">
            <div class="mb-4">
                <x-form.label for="keterangan">Keterangan</x-form.label>
                <x-form.textarea class="w-full mt-1" id="keterangan" name="keterangan" wire:model="keterangan" rows="4" placeholder="Keterangan tambahan (opsional)" />
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-700" for="is_active">
                <input id="is_active" type="checkbox" wire:model="is_active" class="border-gray-300 rounded text-green-600 focus:ring-green-500" />
                Rekening aktif
            </label>
            @error('is_active')
                <div class="mt-1 text-xs text-rose-500">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <x-elements.button-submit class="mt-5" wire:loading.attr="disabled">
        <span wire:loading wire:target="{{ $submitMethod }}" class="animate-spin inline-block size-3 border-[2px] border-current border-t-transparent rounded-full"></span>
        <x-lucide-save wire:loading.remove wire:target="{{ $submitMethod }}" class="size-4" />
        <span>{{ $submitLabel }}</span>
    </x-elements.button-submit>
</form>
