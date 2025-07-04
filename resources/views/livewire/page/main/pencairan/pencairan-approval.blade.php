<div>
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <x-elements.button wire:navigate :href="route('main.pencairan.list')" :variant="'primary'" :style="'outlined'" :type="'link'" class='mb-6'>
        <x-lucide-arrow-left class="size-5"/>
        <span class="xs:block">Back to list page</span>
    </x-elements.button>
    <x-elements.button wire:click='exportPDF' :variant="'info'" :style="'outlined'" :type="'button'">
        <x-lucide-download class="size-5"/>
        <span class="xs:block">Export Form</span>
    </x-elements.button>

    <div class="p-6 mb-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <h4 class='mb-6 text-lg font-bold'>Detail Request Pencairan</h4>
        <hr class='mb-6' />
        <div class="grid grid-cols-2">
            <div>
                <x-elements.detail label="Tanggal Pengajuan">{{ date('d F Y, H:i:s', strtotime($data->tgl_pengajuan)) }}</x-elements.detail>
                <x-elements.detail label="Nama Anggota">{{ $data->masterAnggota->nama }} ({{ $data->masterAnggota->nomor_anggota }})</x-elements.detail>
                <x-elements.detail label="Pencairan Tabungan">{{ $data->jenisTabungan->nama }}</x-elements.detail>
                <x-elements.detail label="Jumlah Diajukan">Rp {{ number_format($data->jumlah_diambil) }}</x-elements.detail>
                <x-elements.detail label="Saldo Saat Ini">Rp {{ number_format($saldoSisa) }}</x-elements.detail>
            </div>
            <div>
                <x-elements.detail label="Alasan Pencairan">{{ $data->catatan_user }}</x-elements.detail>
                <x-elements.detail label="Nomor Rekening">{{ $data->rekening_no }}</x-elements.detail>
                <x-elements.detail label="Nama Bank">{{ $data->rekening_bank }}</x-elements.detail>
            </div>
        </div>
    </div>

    <div class="p-6 mb-6 bg-white border rounded-lg shadow-lg border-slate-200">
        <h4 class='mb-6 text-lg font-bold'>Form Approval</h4>
        <hr class='mb-6' />
        @if($data->status_pengambilan !== 'DISETUJUI')
        <form wire:submit="saveInsert">
            <div class="grid grid-cols-12 gap-10 mb-2">
                <div class="col-span-12 md:col-span-6">
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="jumlah">
                                Jumlah Disetujui <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="jumlah_disetujui" wire:model.lazy="jumlah_disetujui" step="0.01" />
                            </div>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="uraian">
                                Tanggal Pencairan
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="date" name="tgl_pencairan" wire:model.lazy="tgl_pencairan"/>
                            </div>
                        </div>
                    </div>
                    <div class="grid items-start grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="remarks">Catatan Admin <span class="text-red-500">*</span></x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.textarea class="w-full" type="text" name="catatan_approver" wire:model.lazy="catatan_approver"/>
                            </div>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="uraian">
                                Status Approval <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.select-single name="status_pencairan" wire:model.lazy="status_pencairan" class="w-full">
                                    <option value="PENDING">
                                        PENDING
                                    </option>
                                    <option value="DIVERIFIKASI">
                                        DIVERIFIKASI
                                    </option>
                                    <option value="DISETUJUI">
                                        DISETUJUI
                                    </option>
                                    <option value="DITOLAK">
                                        DITOLAK
                                    </option>
                                </x-form.select-single>
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
        @else
        <div class="grid grid-cols-2">
            <div>
                <x-elements.detail label="Status Approval">{{ $data->status_pengambilan.' ('.date('d F Y, H:i:s', strtotime($data->updated_at)).')' }}</x-elements.detail>
                <x-elements.detail label="Jumlah Disetujui">Rp {{ number_format($data->jumlah_disetujui) }}</x-elements.detail>
                <x-elements.detail label="Tanggal Pencairan">{{ date('d F Y, H:i:s', strtotime($data->tgl_pencairan)) }}</x-elements.detail>
                <x-elements.detail label="Catatan Admin">{{ $catatan_approver }}</x-elements.detail>
            </div>
        </div>
        @endif
    </div>

</div>
