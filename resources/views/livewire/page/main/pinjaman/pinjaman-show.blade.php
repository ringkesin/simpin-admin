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
            <x-elements.detail label="Telp">{{ $this->setIfNull($loadData['masterAnggota']['userId']['mobile'],'-') }}</x-elements.detail>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Detail Pinjaman</x-header-form>
            <x-elements.detail label="Jenis Pinjaman">{{ $this->setIfNull($loadData['masterJenisPinjaman']['nama'],'-') }}</x-elements.detail>
            <x-elements.detail label="RA Pinjaman">{{ $this->setIfNull('Rp. '.$this->toRupiah($loadData['ra_jumlah_pinjaman']),'-') }}</x-elements.detail>
            <x-elements.detail label="Alamat yang didaftarkan">{{ $this->setIfNull($loadData['alamat'],'-') }}</x-elements.detail>
            <x-elements.detail label="No. Rekening">{{ $this->setIfNull($loadData['no_rekening'],'-') }}</x-elements.detail>
            <x-elements.detail label="Nama Bank">{{ $this->setIfNull($loadData['bank'],'-') }}</x-elements.detail>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Dokumen Peminjam</x-header-form>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-elements.detail label="KTP">
                        @php
                            $pp = ($loadData['doc_ktp']) ? '/storage/'.$loadData['doc_ktp'] : asset('assets/img/blank-doc.png');
                        @endphp
                        <img class='rounded-2xl' src="{{ $pp }}" width="130">
                    </x-elements.detail>
                    <x-elements.detail label="Surat Nikah">
                        @php
                            $pp = ($loadData['doc_surat_nikah']) ? '/storage/'.$loadData['doc_surat_nikah'] : asset('assets/img/blank-doc.png');
                        @endphp
                        <img class='rounded-2xl' src="{{ $pp }}" width="130">
                    </x-elements.detail>
                </div>
                <div>
                    <x-elements.detail label="Kartu Keluarga">
                        @php
                            $pp = ($loadData['doc_kk']) ? '/storage/'.$loadData['doc_kk'] : asset('assets/img/blank-doc.png');
                        @endphp
                        <img class='rounded-2xl' src="{{ $pp }}" width="130">
                    </x-elements.detail>
                    <x-elements.detail label="Slip Gaji">
                        @php
                            $pp = ($loadData['doc_slip_gaji']) ? '/storage/'.$loadData['doc_slip_gaji'] : asset('assets/img/blank-doc.png');
                        @endphp
                        <img class='rounded-2xl' src="{{ $pp }}" width="130">
                    </x-elements.detail>
                </div>
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
