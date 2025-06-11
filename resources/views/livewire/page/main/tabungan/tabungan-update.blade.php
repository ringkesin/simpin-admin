<div>
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <x-elements.button wire:navigate :href="route('main.tabungan.list')" :variant="'success'" :style="'outlined'" :type="'link'" class='mb-6'>
        <x-lucide-arrow-left class="size-5"/>
        <span class="xs:block">Back to list page</span>
    </x-button>
    
    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200 mb-6">
        <h4 class='mb-6 text-lg font-bold'>Saldo Tabungan</h4>
        <hr class='mb-6' />
        @livewire('page.main.tabungan.tabungan-anggota-saldo-table', ['p_anggota_id' => $id])
    </div>
    <div class="p-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <h4 class='mb-6 text-lg font-bold'>Mutasi Tabungan</h4>
        <hr class='mb-6' />
        <form wire:submit="saveInsert">
            <div class="grid grid-cols-12 gap-10 mb-2">
                <div class="col-span-12 md:col-span-6">
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="uraian">
                                Tanggal Transaksi <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="date" name="tglTransaksi" wire:model.lazy="tglTransaksi"/>
                            </div>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="uraian">
                                Pilih Tabungan <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                {{-- <x-form.input class="w-full" type="number" name="jumlah" wire:model.lazy="jumlah"/> --}}
                                <x-form.select-single name="jenisTabunganId" wire:model.lazy="jenisTabunganId" class="w-full">
                                    <option value="">- Pilih -</option>
                                    @foreach($dataJenisTabungan as $d)
                                        <option value="{{ $d->p_jenis_tabungan_id }}">{{ $d->nama }}</option>
                                    @endforeach
                                </x-form.select-single>
                            </div>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jumlah">
                                Jumlah <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="jumlah" wire:model.lazy="jumlah" step="0.01" />
                            </div>
                        </div>
                    </div>
                    <div class="grid items-start grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="remarks">Catatan</x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.textarea class="w-full" type="text" name="remarks" wire:model.lazy="remarks"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-elements.button-submit wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
                <div wire:loading wire:target="saveInsert">
                    <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading">
                        <span class="sr-only">Processing.....</span>
                    </span>
                    <span class="xs:block">
                        Processing
                    </span>
                </div>
                <div class='flex gap-x-1' wire:loading.remove wire:target="saveInsert">
                    <x-lucide-save class="size-5"/>
                    <span class="xs:block">
                        Save data
                    </span>
                </div>
            </x-elements.button-submit>
        </form>
        
        <hr class='mt-8 mb-6' />
        
        @livewire('page.main.tabungan.tabungan-anggota-table', ['p_anggota_id' => $id])
    </div>
</div>
