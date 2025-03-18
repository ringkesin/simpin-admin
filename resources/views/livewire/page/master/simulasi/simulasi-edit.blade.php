<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="grid grid-cols-2 mb-6 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">
        <!----------------Action Button------------------------>
        <div class='px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200'>
            <div class="flex justify-between gap-2">
                <div>
                <x-elements.button :href="route('master.simulasi.show', ['id' => $loadData['id']])" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to view page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <form wire:submit="saveUpdate">
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Nomor Anggota -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="pinjaman">
                                Jumlah Pinjaman <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="pinjaman" wire:model.lazy="pinjaman"/>
                        </div>
                    </div>
                    <!-- Group Input Nama -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="margin">
                                Margin / Bunga <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="margin" wire:model.lazy="margin"/>
                        </div>
                    </div>
                    <!-- Group Input NIK -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tahun_margin">
                                Tahun <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="tahun_margin" wire:model.lazy="tahun_margin"/>
                        </div>
                    </div>
                    <!-- Group Input KTP -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            {{-- <x-form.label for="ktp">
                                No KTP
                            </x-form.label> --}}
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{-- <x-form.input class="w-full" type="text" name="ktp" wire:model.lazy="ktp"/> --}}
                        </div>
                    </div>
                    <!-- Group Input Alamat -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            {{-- <x-form.label for="alamat">
                                Alamat
                            </x-form.label> --}}
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{-- <x-form.input class="w-full" type="text" name="alamat" wire:model.lazy="alamat"/> --}}
                        </div>
                    </div>
                    <!-- Group Input Tanggal Lahir -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            {{-- <x-form.label for="tgl_lahir">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </x-form.label> --}}
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{-- <x-form.input class="w-full" type="date" id="tgl_lahir" name="tgl_lahir" :value="''" wire:model.lazy="tgl_lahir"/> --}}
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tenor">
                                Tenor <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="tenor" id="tenor" name="tenor" :value="''" wire:model.lazy="tenor"/>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="angsuran">
                                Angsuran  <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="angsuran" id="angsuran" name="angsuran" :value="''" wire:model.lazy="angsuran"/>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            {{-- <x-form.label for="valid_to">
                                Valid Sampai
                            </x-form.label> --}}
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{-- <x-form.input class="w-full" type="date" id="valid_to" name="valid_to" :value="''" wire:model.lazy="valid_to"/> --}}
                        </div>
                    </div>
                </div>
            </div>
            <x-elements.button-submit class="mt-5" wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
                <div wire:loading wire:target="saveUpdate">
                    {{-- <svg class="w-5 h-5 mr-3 animate-spin" viewBox="0 0 24 24"> --}}
                        {{-- <span class="sr-only">Loading Save Data</span> --}}
                    {{-- </svg> --}}
                    <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading">
                        <span class="sr-only">Processing.....</span>
                    </span>
                    <span class="xs:block">
                        Processing
                    </span>
                </div>
                <div class='flex gap-x-1' wire:loading.remove wire:target="saveUpdate">
                    <x-lucide-square-pen class="size-5"/>
                    <span class="xs:block">
                        Update data
                    </span>
                </div>
            </x-elements.button-submit>
        </form>
    </div>
</div>
