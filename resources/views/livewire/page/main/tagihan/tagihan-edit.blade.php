<div>
    {{-- The whole world belongs to you. --}}
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
                    <x-elements.button wire:navigate :href="route('main.tagihan.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <form wire:submit="saveUpdate">
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
                                    @foreach ($this->loadDataAnggota as $arrayLoop)
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
                            <x-form.select-single name="bulan" wire:model.lazy="bulan" class="w-full md:w-2/4" id="bulan">
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
                            <x-form.select-single name="tahun" wire:model.lazy="tahun" class="w-full md:w-2/4" id="tahun">
                                <option value="">Pilih Tahun</option>
                                @foreach($this->getYearRange(2) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------Tagihan Section------------------------>
            <x-elements.header-form>Tagihan Detail</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Relasi Pinjaman  -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="t_pinjaman_id">
                                Relasi Pinjaman
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select-single name="t_pinjaman_id" wire:model.lazy="t_pinjaman_id" class="w-full" id="t_pinjaman_id">
                                <option value="">Pilih Pinjaman</option>
                                @foreach($this->loadPinjaman as $pinjaman)
                                    <option value="{{ $pinjaman['t_pinjaman_id'] }}">{{ $pinjaman['nomor_pinjaman'] }}</option>
                                @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                    <!-- Group Simpanan Pokok -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="uraian">
                                Uraian <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.textarea class="w-full" type="text" name="uraian" wire:model.lazy="uraian"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Jumlah -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jumlah">
                                Jumlah <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="jumlah" wire:model.lazy="jumlah"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Remarks -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="remarks">
                                Deskripsi/Catatan
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.textarea class="w-full" type="text" name="remarks" wire:model.lazy="remarks"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------Detail Pembayaran Section------------------------>
            <x-elements.header-form>Detail Pembayaran</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Tanggal Jatuh Tempo  -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tgl_jatuh_tempo">
                                Tanggal Jatuh Tempo
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" name="tgl_jatuh_tempo" wire:model.lazy="tgl_jatuh_tempo"/>
                        </div>
                    </div>
                    <!-- Group Status Pembayaran -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="p_status_pembayaran_id">
                                Status Pembayaran <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select-single name="p_status_pembayaran_id" wire:model.lazy="p_status_pembayaran_id" class="w-full" id="p_status_pembayaran_id">
                                <option value="">Pilih Status Pembayaran</option>
                                @foreach($this->loadStatusPembayaran as $statsPem)
                                    <option value="{{ $statsPem['p_status_pembayaran_id'] }}">{{ $statsPem['status_code'] }} - {{ $statsPem['status_name'] }}</option>
                                @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                </div>
                @if($p_status_pembayaran_id == 2)
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Tanggal Dibayar  -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="paid_at">
                                Tanggal Dibayar
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" name="paid_at" wire:model.lazy="paid_at"/>
                        </div>
                    </div>
                    <!-- Group Jumlah Dibayarkan -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jumlah_pembayaran">
                                Jumlah Dibayarkan
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="jumlah_pembayaran" wire:model.lazy="jumlah_pembayaran"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Metode Pembayaran -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="p_metode_pembayaran_id">
                                Metode Pembayaran
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select-single name="p_metode_pembayaran_id" wire:model.lazy="p_metode_pembayaran_id" class="w-full" id="p_metode_pembayaran_id">
                                <option value="">Pilih Metode Pembayaran</option>
                                @foreach($this->loadMetodePembayaran as $methodPem)
                                    <option value="{{ $methodPem['p_metode_pembayaran_id'] }}">{{ $methodPem['metode_code'] }} - {{ $methodPem['metode_name'] }}</option>
                                @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <x-elements.header-form></x-elements.header-form>
            <div class="grid grid-cols-12 gap-10">
                <div class="col-span-12">
                    <x-elements.button-submit wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
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
                                Save data
                            </span>
                        </div>
                    </x-elements.button-submit>
                </div>
            </div>
        </form>
    </div>
</div>
