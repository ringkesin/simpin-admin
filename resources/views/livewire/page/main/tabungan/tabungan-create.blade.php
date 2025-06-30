<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
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
                    <x-elements.button wire:navigate :href="route('main.tabungan.list')" :variant="'primary'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <form wire:submit="saveInsertV2">
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Anggota -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="p_anggota_id">
                                Nama Anggota <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select2-single name="p_anggota_id" wire:model.lazy="p_anggota_id" class="w-full md:w-full" id="p_anggota_id">
                                <option value="">Pilih Anggota</option>
                                    @foreach ($this->loadData as $arrayLoop)
                                        <option value="{{ $arrayLoop['p_anggota_id'] }}">{{ $arrayLoop['nomor_anggota'] }} - {{ $arrayLoop['nama'] }}</option>
                                    @endforeach
                            </x-form.select2-single>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------Period Section------------------------>
            <x-elements.header-form>Periode</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Bulan -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="bulan">
                                Bulan <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select-single name="bulan" wire:model.lazy="bulan" class="w-full" id="bulan">
                                <option value="">Pilih Bulan</option>
                                    @foreach ($this->listMonth() as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Tahun -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tahun">
                                Tahun <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select-single name="tahun" wire:model.lazy="tahun" class="w-full" id="tahun">
                                <option value="">Pilih Tahun</option>
                                @foreach($this->getYearRange(2) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------Tabungan Section------------------------>
            <x-elements.header-form>Nilai Tabungan</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Simpanan Pokok -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="simpanan_pokok">
                                Simpanan Pokok <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="simpanan_pokok" wire:model.lazy="simpanan_pokok"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Simpanan Wajib -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="simpanan_wajib">
                                Simpanan Wajib <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="simpanan_wajib" wire:model.lazy="simpanan_wajib"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Tabungan Sukarela -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tabungan_sukarela">
                                Tabungan Sukarela <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="tabungan_sukarela" wire:model.lazy="tabungan_sukarela"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Tabungan Indir -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tabungan_indir">
                                Tabungan Indir <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="tabungan_indir" wire:model.lazy="tabungan_indir"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Kompensasi Masa Kerja -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="kompensasi_masa_kerja">
                                Kompensasi Masa Kerja <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="kompensasi_masa_kerja" wire:model.lazy="kompensasi_masa_kerja"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!----------------Keterangan Section------------------------>
            <x-elements.header-form class='!mb-0 !border-white'>Keterangan</x-header-form>
            <div class='mb-8'>
                <x-form.textarea class="w-full" type="text" name="keterangan" wire:model.lazy="keterangan"/>
            </div>

            <div class="grid grid-cols-12 gap-10">
                <div class="col-span-12">
                    <x-elements.button-submit wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
                        <div wire:loading wire:target="saveInsertV2">
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
                        <div class='flex gap-x-1' wire:loading.remove wire:target="saveInsertV2">
                            <x-lucide-square-pen class="size-5"/>
                            <span class="xs:block">
                                Save data
                            </span>
                        </div>
                    </x-elements.button-submit>
                </div>
            </div>
        </form>
    </div>
</div>
