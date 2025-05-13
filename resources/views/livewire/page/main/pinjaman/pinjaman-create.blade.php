<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
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
        <form wire:submit="saveInsert">
            <x-elements.header-form>Detail Pinjaman</x-header-form>
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
                    <!-- Group Jenis Pinjaman -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="p_jenis_pinjaman_id">
                                Jenis Pinjaman <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select2-single name="p_jenis_pinjaman_id" wire:model.lazy="p_jenis_pinjaman_id" class="w-full md:w-full" id="p_jenis_pinjaman_id">
                                <option value="">Pilih Jenis Pinjaman</option>
                                    @foreach ($this->loadDataJenisPinjaman as $arrayLoop)
                                        <option value="{{ $arrayLoop['p_jenis_pinjaman_id'] }}">{{ $arrayLoop['kode_jenis_pinjaman'] }} - {{ $arrayLoop['nama'] }}</option>
                                    @endforeach
                            </x-form.select2-single>
                        </div>
                    </div>
                    @if($p_jenis_pinjaman_id == 3)
                    <!-- Group Jenis Barang -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jenis_barang">
                                Jenis Barang <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="jenis_barang" wire:model.lazy="jenis_barang"/>
                        </div>
                    </div>

                    <!-- Group Merk/Tipe Barang -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="merk_type">
                                Merk/Tipe Barang <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="merk_type" wire:model.lazy="merk_type"/>
                        </div>
                    </div>
                    @endif

                    <!-- Group Jumlah Pinjaman -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="ra_jumlah_pinjaman">
                                Jumlah Pinjaman <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="number" name="ra_jumlah_pinjaman" wire:model.lazy="ra_jumlah_pinjaman"/>
                        </div>
                    </div>
                    <!-- Group Tenor -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tenor">
                                Tenor (Bulan) <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="number" name="tenor" wire:model.lazy="tenor"/>
                        </div>
                    </div>
                    <!-- Group Keperluan -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="pinjaman_keperluan">
                                Keperluan @if($p_jenis_pinjaman_id != 3)<span class="text-red-500">*</span>@endif
                                <x-elements.button type='button' variant="success" style='solid' class='justify-center'
                                        wire:click="addRow()" wire:loading.attr="disabled" wire:loading.class="cursor-progress" >
                                    <div wire:loading wire:target="addRow()">
                                        <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-red-300 dark:text-slate-300 rounded-full" role="status" aria-label="loading">
                                            <span class="sr-only"></span>
                                        </span>
                                    </div>
                                    <span class="hidden md:flex" wire:loading.remove wire:target="addRow()"> <x-lucide-circle-plus class="size-3"/></span>
                                    <span class="flex md:hidden" wire:loading.remove wire:target="addRow()"> <x-ph-trash class='!me-0' /></span>
                                </x-elements.button>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            @foreach ($p_pinjaman_keperluan_ids as $index => $detail)
                            <div class="flex gap-4 mb-2">
                                {{-- <x-form.input class="w-full" type="text" name="p_pinjaman_keperluan_ids.{{ $index }}" wire:model.lazy.defer="p_pinjaman_keperluan_ids.{{ $index }}"/> --}}
                                <x-form.select-single name="p_pinjaman_keperluan_ids.{{ $index }}" wire:model.lazy.defer="p_pinjaman_keperluan_ids.{{ $index }}" class="w-full md:w-full" id="p_pinjaman_keperluan_ids.{{ $index }}">
                                    <option value="">Pilih Keperluan</option>
                                        @foreach ($this->loadDataKeperluanPinjaman as $arrayLoop)
                                            <option value="{{ $arrayLoop['p_pinjaman_keperluan_id'] }}">{{ $arrayLoop['keperluan'] }}</option>
                                        @endforeach
                                </x-form.select-single>
                                <div class="grid justify-center grid-cols-1 gap-4 ml-3 place-items-center">
                                    <x-elements.button type='button' variant="danger" style='solid' class='justify-center'
                                            wire:click="removeRow({{ $index }})" wire:loading.attr="disabled" wire:loading.class="cursor-progress" >
                                        <div wire:loading wire:target="removeRow({{ $index }})">
                                            <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-red-300 dark:text-slate-300 rounded-full" role="status" aria-label="loading">
                                                <span class="sr-only"></span>
                                            </span>
                                        </div>
                                        <span class="hidden md:flex" wire:loading.remove wire:target="removeRow({{ $index }})"> <x-lucide-circle-minus class="size-3"/> </span>
                                        <span class="flex md:hidden" wire:loading.remove wire:target="removeRow({{ $index }})"> <x-ph-trash class='!me-0' /></span>
                                    </x-elements.button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Jaminan -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jaminan">
                                Jaminan <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="jaminan" wire:model.lazy="jaminan"/>
                        </div>
                    </div>
                    <!-- Group Jaminan Keterangan -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jaminan_keterangan">
                                Keterangan Jaminan
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="jaminan_keterangan" wire:model.lazy="jaminan_keterangan"/>
                        </div>
                    </div>
                    <!-- Group Jaminan Perkiraan Nilai -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jaminan_perkiraan_nilai">
                                Perkiraan Nilai Jaminan <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="jaminan_perkiraan_nilai" wire:model.lazy="jaminan_perkiraan_nilai"/>
                        </div>
                    </div>
                    <!-- Group Nomor Rekening -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="no_rekening">
                                Nomor Rekening <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="no_rekening" wire:model.lazy="no_rekening"/>
                        </div>
                    </div>
                    <!-- Group Bank -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="bank">
                                Bank <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="bank" wire:model.lazy="bank"/>
                        </div>
                    </div>
                </div>
            </div>
            <x-elements.header-form>Dokumen Peminjam</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Slip Gaji -->
                    <div x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"  class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="doc_slip_gaji">
                                Slip Gaji <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="flex gap-5">
                                <div>
                                    <x-form.input type="file" name="doc_slip_gaji" wire:model.lazy="doc_slip_gaji" />
                                </div>
                                <div x-show="uploading" class="relative w-full h-4 mt-4 bg-gray-200 rounded-full shadow-inner md:mt-0 md:ml-4">
                                    <div class="absolute top-0 left-0 h-4 bg-blue-500 rounded-full"
                                        x-bind:style="`width: ${progress}%;`"></div>
                                </div>
                                <div x-show="uploading" class="px-2 text-sm text-center text-gray-500">
                                    Uploading... <span x-text="`${progress}%`"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-elements.header-form>Approval</x-header-form>
            <x-elements.detail label="Jumlah Pinjaman yang Disetujui" required="true">
                <div class="flex">
                    <div class="flex-none w-8">
                        Rp.
                    </div>
                    <div class="flex-initial w-64">
                        <x-form.input class="w-full" type="number" name="ri_jumlah_pinjaman" wire:model.lazy="ri_jumlah_pinjaman"/>
                    </div>
                </div>
            </x-elements.detail>
            <x-elements.detail label="Margin (%)" required="true">
                <div class="flex gap-4">
                    <div class="flex-initial w-20">
                        <x-form.input class="w-full" type="text" name="margin" wire:model.lazy="margin"/>
                    </div>
                    <div class="flex-initial w-54">
                        = Rp. {{ $this->toRupiah($ri_jumlah_pinjaman * ($margin / 100)) }}
                    </div>
                </div>
            </x-elements.detail>
            <x-elements.detail label="Biaya Admin (%)" required="true">
                <div class="flex gap-4">
                    <div class="flex-initial w-20">
                        <x-form.input class="w-full" type="text" name="biaya_admin" wire:model.lazy="biaya_admin"/>
                    </div>
                    <div class="flex-initial w-54">
                        = Rp. {{ $this->toRupiah($ri_jumlah_pinjaman * ($biaya_admin / 100)) }}
                    </div>
                </div>
            </x-elements.detail>
            <x-elements.detail label="Total Jumlah Disetujui + Margin">
                <div class="flex gap-4">
                    <div class="flex-none w-8">
                        Rp.
                    </div>
                    <div class="flex-initial w-64">
                        {{ $this->toRupiah($this->totalDisetujui()) }}
                    </div>
                </div>
            </x-elements.detail>
            <x-elements.detail label="Status Pinjaman" required="true">
                <x-form.select-single name="p_status_pengajuan_id" wire:model="p_status_pengajuan_id" class="w-full md:w-72" id="p_status_pengajuan_id">
                    <option value="">- Pilih Status Pengajuan -</option>
                        @foreach ($this->listStatusPinjaman() as $value)
                            <option value="{{ $value['p_status_pengajuan_id'] }}">{{ $value['nama'] }}</option>
                        @endforeach
                </x-form.select-single>
            </x-elements.detail>
            <x-elements.detail label="Catatan">
                <div class="flex gap-4">
                    <div class="flex-initial w-full md:w-72">
                        <x-form.textarea class="w-full" type="text" name="remarks" wire:model.lazy="remarks"/>
                    </div>
                </div>
            </x-elements.detail>
            <x-elements.detail label="Tanggal Pencairan">
                <div class="flex gap-4">
                    <div class="flex-initial w-full md:w-72">
                        <x-form.input class="w-full" type="date" name="tgl_pencairan" wire:model.lazy="tgl_pencairan"/>
                    </div>
                </div>
            </x-elements.detail>
            <x-elements.detail label="Tanggal Pelunasan">
                <div class="flex gap-4">
                    <div class="flex-initial w-full md:w-72">
                        <x-form.input class="w-full" type="date" name="tgl_pelunasan" wire:model.lazy="tgl_pelunasan"/>
                    </div>
                </div>
            </x-elements.detail>
            <div class="grid grid-cols-12 gap-10 mt-6">
                <div class="col-span-12">
                    <x-elements.button-submit wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
                        <div wire:loading wire:target="saveInsert">
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
                        <div class='flex gap-x-1' wire:loading.remove wire:target="saveInsert">
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
