<div>
    {{-- The Master doesn't talk, he acts. --}}
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
                    <x-elements.button wire:navigate :href="route('main.pinjaman.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <div class='md:pb-3 md:px-6'>
            <div class="mt-5">
                <x-elements.header-form>Detail Anggota</x-header-form>
                <div class="grid grid-cols-2">
                    <div>
                        <x-elements.detail label="Nomor Anggota">{{ $this->setIfNull($loadData['master_anggota']['nomor_anggota'], '-') }}</x-elements.detail>
                        <x-elements.detail label="Nama Anggota">{{ $this->setIfNull($loadData['master_anggota']['nama'],'-') }}</x-elements.detail>
                        <x-elements.detail label="Telp">{{ $this->setIfNull($loadData['master_anggota']['mobile'],'-') }}</x-elements.detail>
                    </div>
                    <div>
                        <x-elements.detail label="Alamat">{{ $this->setIfNull($loadData['master_anggota']['alamat'],'-') }}</x-elements.detail>
                        <x-elements.detail label="Email">{{ $this->setIfNull($loadData['master_anggota']['email'],'-') }}</x-elements.detail>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <x-elements.header-form>Detail Pinjaman</x-header-form>
                <div class="grid grid-cols-2">
                    <div>
                        <x-elements.detail label="Jenis Pinjaman">{{ $this->setIfNull($loadData['master_jenis_pinjaman']['nama'],'-') }}</x-elements.detail>
                        @if($loadData['p_jenis_pinjaman_id'] <> 3)
                        <x-elements.detail label="Keperluan">
                            <ol class="list-decimal list-inside">
                            @php $no_keperluan = 1; @endphp
                            @foreach ($loadData['keperluan'] as $keperluan)
                                <li>{{ $keperluan }}</li>
                            @endforeach
                            </ol>
                        </x-elements.detail>
                        @endif
                        @if($loadData['p_jenis_pinjaman_id'] == 3)
                        <x-elements.detail label="Jenis Barang">{{ $this->setIfNull($loadData['jenis_barang'],'-') }}</x-elements.detail>
                        <x-elements.detail label="Merek Tipe">{{ $this->setIfNull($loadData['merk_type'],'-') }}</x-elements.detail>
                        @endif
                        <x-elements.detail label="Jumlah Pengajuan">{{ $this->setIfNull('Rp. '.$this->toRupiah($loadData['ra_jumlah_pinjaman']),'-') }}</x-elements.detail>
                        <x-elements.detail label="Tenor">{{ $this->setIfNull($loadData['tenor'],'-') }} Bulan</x-elements.detail>
                    </div>
                    <div>
                        <x-elements.detail label="Jaminan">{{ $this->setIfNull($loadData['jaminan'],'-') }}</x-elements.detail>
                        <x-elements.detail label="Keterangan Jaminan">{{ $this->setIfNull($loadData['jaminan_keterangan'],'-') }}</x-elements.detail>
                        <x-elements.detail label="Perkiraan Nilai Jaminan">{{ $this->setIfNull('Rp. '.$this->toRupiah($loadData['jaminan_perkiraan_nilai'],'-')) }}</x-elements.detail>
                        <x-elements.detail label="No. Rekening">{{ $this->setIfNull($loadData['no_rekening'],'-') }}</x-elements.detail>
                        <x-elements.detail label="Nama Bank">{{ $this->setIfNull($loadData['bank'],'-') }}</x-elements.detail>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <x-elements.header-form>Dokumen Peminjam</x-header-form>
                <div class="grid grid-cols-2">
                    <div>
                        <x-elements.detail label="KTP Pemohon">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_ktp_sec'] }}' target='_blank'>
                                Preview <x-lucide-external-link class="w-4"/>
                            </a>
                        </x-elements.detail>
                        <x-elements.detail label="KTP Suami / Istri">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_ktp_suami_istri_sec'] }}' target='_blank'>
                                Preview <x-lucide-external-link class="w-4"/>
                            </a>
                        </x-elements.detail>
                        <x-elements.detail label="Kartu Keluarga">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_kk_sec'] }}' target='_blank'>
                                Preview <x-lucide-external-link class="w-4"/>
                            </a>
                        </x-elements.detail>
                    </div>
                    <div>
                        <x-elements.detail label="ID Card Pegawai">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_kartu_anggota'] }}' target='_blank'>
                                Preview <x-lucide-external-link class="w-4"/>
                            </a>
                        </x-elements.detail>
                        <x-elements.detail label="Slip Gaji Terakhir">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_slip_gaji'] }}' target='_blank'>
                                Preview <x-lucide-external-link class="w-4"/>
                            </a>
                        </x-elements.detail>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <x-elements.header-form>Detail Atribut Anggota</x-header-form>
                <div class="grid grid-cols-2">
                    @foreach ($loadDataAttr as $attr)
                        <x-elements.detail label="{{ $attr['atribut_kode'] }}">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $attr['atribut_attachment'] }}' target='_blank'>
                                {{ $attr['atribut_value'] }} <x-lucide-external-link class="w-4"/>
                            </a>
                        </x-elements.detail>
                    @endforeach
                </div>
            </div>
            <div class="mt-5">
                <x-elements.header-form>Approval</x-header-form>
                <form wire:submit="saveApproval">
                    <x-elements.detail label="Jumlah Pinjaman yang Disetujui">
                        <div class="flex">
                            <div class="flex-none w-8">
                                Rp.
                            </div>
                            <div class="flex-initial w-64">
                                <x-form.input class="w-full" type="number" name="ri_jumlah_pinjaman" wire:model="ri_jumlah_pinjaman"/>
                            </div>
                        </div>
                    </x-elements.detail>
                    <x-elements.detail label="Status Pinjaman">
                        <x-form.select-single name="p_status_pengajuan_id" wire:model="p_status_pengajuan_id" class="w-full md:w-72" id="p_status_pengajuan_id">
                            <option value="">- Pilih Status Pengajuan -</option>
                                @foreach ($this->listStatusPinjaman() as $value)
                                    <option value="{{ $value['p_status_pengajuan_id'] }}">{{ $value['nama'] }}</option>
                                @endforeach
                        </x-form.select-single>
                    </x-elements.detail>
                    <div class="grid grid-cols-12 gap-10 mt-6">
                        <div class="col-span-12">
                            <x-elements.button-submit wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
                                <div wire:loading wire:target="saveApproval">
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
                                <div class='flex gap-x-1' wire:loading.remove wire:target="saveApproval">
                                    <x-lucide-save class="size-5"/>
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
    </div>
</div>
