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
        <div class="mt-5">
            <x-elements.header-form>Detail Anggota</x-header-form>
            <x-elements.detail label="Nomor Anggota">{{ $this->setIfNull($loadData['masterAnggota']['nomor_anggota'], '-') }}</x-elements.detail>
            <x-elements.detail label="Nama Anggota">{{ $this->setIfNull($loadData['masterAnggota']['nama'],'-') }}</x-elements.detail>
            <x-elements.detail label="Telp">{{ $this->setIfNull($loadData['masterAnggota']['userId']['mobile'],'-') }}</x-elements.detail>
            <x-elements.detail label="Alamat">{{ $this->setIfNull($loadData['masterAnggota']['userId']['alamat'],'-') }}</x-elements.detail>
            <x-elements.detail label="Email">{{ $this->setIfNull($loadData['masterAnggota']['userId']['email'],'-') }}</x-elements.detail>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Detail Pinjaman</x-header-form>
            <x-elements.detail label="Jenis Pinjaman">{{ $this->setIfNull($loadData['masterJenisPinjaman']['nama'],'-') }}</x-elements.detail>
            <x-elements.detail label="RA Pinjaman">{{ $this->setIfNull('Rp. '.$this->toRupiah($loadData['ra_jumlah_pinjaman']),'-') }}</x-elements.detail>
            @if($loadData['p_jenis_pinjaman_id'] == 3)
            <x-elements.detail label="Jenis Barang">{{ $this->setIfNull($loadData['jenis_barang'],'-') }}</x-elements.detail>
            <x-elements.detail label="Merek Tipe">{{ $this->setIfNull($loadData['merk_type'],'-') }}</x-elements.detail>
            @endif
            <x-elements.detail label="Jaminan">{{ $this->setIfNull($loadData['jaminan'],'-') }}</x-elements.detail>
            <x-elements.detail label="Keterangan Jaminan">{{ $this->setIfNull($loadData['jaminan_keterangan'],'-') }}</x-elements.detail>
            <x-elements.detail label="Perkiraan Nilai Jaminan">{{ $this->setIfNull('Rp. '.$this->toRupiah($loadData['jaminan_perkiraan_nilai'],'-')) }}</x-elements.detail>
            <x-elements.detail label="No. Rekening">{{ $this->setIfNull($loadData['no_rekening'],'-') }}</x-elements.detail>
            <x-elements.detail label="Nama Bank">{{ $this->setIfNull($loadData['bank'],'-') }}</x-elements.detail>
            <x-elements.detail label="Tenor">{{ $this->setIfNull($loadData['tenor'],'-') }}</x-elements.detail>
            <x-elements.detail label="Keperluan">
                <ol class="list-decimal list-inside">
                @php $no_keperluan = 1; @endphp
                @foreach ($loadData['keperluan'] as $keperluan)
                    <li>{{ $keperluan['keperluan_nama'] }}</li>
                @endforeach
                </ol>
            </x-elements.detail>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Dokumen Peminjam</x-header-form>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="doc_ktp">
                                KTP
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_ktp_sec'] }}' target='_blank'>
                                <p class="w-64 overflow-hidden text-ellipsis whitespace-nowrap">{{ $loadData['doc_ktp_name'] }}</p> <x-lucide-external-link class="w-4"/>
                            </a>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="doc_ktp_suami_istri">
                                Surat Nikah
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_ktp_suami_istri_sec'] }}' target='_blank'>
                                <p class="w-64 overflow-hidden text-ellipsis whitespace-nowrap">{{ $loadData['doc_ktp_suami_istri_name'] }}</p> <x-lucide-external-link class="w-4"/>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="doc_kk">
                                Kartu Keluarga
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_kk_sec'] }}' target='_blank'>
                                <p class="w-64 overflow-hidden text-ellipsis whitespace-nowrap">{{ $loadData['doc_kk_name'] }}</p> <x-lucide-external-link class="w-4"/>
                            </a>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="doc_slip_gaji">
                                Slip Gaji
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $loadData['doc_slip_gaji_sec'] }}' target='_blank'>
                                <p class="w-64 overflow-hidden text-ellipsis whitespace-nowrap">{{ $loadData['doc_slip_gaji_name'] }}</p> <x-lucide-external-link class="w-4"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Detail Atribut Anggota</x-header-form>
            <div class="grid grid-cols-2 gap-4">
                @foreach ($loadDataAttr as $attr)
                    {{-- <div>
                        <x-elements.detail label="{{ $attr['atribut_value'] }}">
                            @php
                                $pp = ($attr['atribut_attachment']) ? '/storage/'.$attr['atribut_attachment'] : asset('assets/img/blank-doc.png');
                            @endphp
                            <img class='rounded-2xl' src="{{ $pp }}" width="130">
                        </x-elements.detail>
                    </div> --}}
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="p_anggota_id">
                                {{ $attr['atribut_kode'] }}
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <a class='inline-flex items-center text-blue-500 gap-x-1' href='{{ $attr['atribut_attachment'] }}' target='_blank'>
                                {{ $attr['atribut_value'] }} <x-lucide-external-link class="w-4"/>
                            </a>
                        </div>
                    </div>
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
                            <x-form.input class="w-full" type="number" name="ri_jumlah_pinjaman" wire:model.lazy="ri_jumlah_pinjaman"/>
                        </div>
                    </div>
                </x-elements.detail>
                <x-elements.detail label="Prakiraan Nilai Pasar">
                    <div class="flex">
                        <div class="flex-none w-8">
                            Rp.
                        </div>
                        <div class="flex-initial w-64">
                            <x-form.input class="w-full" type="number" name="prakiraan_nilai_pasar" wire:model.lazy="prakiraan_nilai_pasar"/>
                        </div>
                    </div>
                </x-elements.detail>
                <x-elements.detail label="Status Pengajuan">
                    <x-form.select-single name="p_status_pengajuan_id" wire:model.lazy="p_status_pengajuan_id" class="w-full md:w-72" id="p_status_pengajuan_id">
                        <option value="">Pilih Prakiraan Nilai Pasar</option>
                            @foreach ($this->listStatusPinjaman() as $value)
                                <option value="{{ $value['p_status_pengajuan_id'] }}">{{ $value['nama'] }}</option>
                            @endforeach
                    </x-form.select-single>
                </x-elements.detail>
                <div class="grid grid-cols-12 gap-10">
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
</div>
