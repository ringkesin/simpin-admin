<div>
    {{-- In work, do what you enjoy. --}}
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
                    <x-elements.button wire:navigate :href="route('main.tagihan.list')" :variant="'primary'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
                <div>
                    <x-elements.button wire:navigate :href="route('main.tagihan.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
                        <x-lucide-square-pen class="size-5"/>
                        <span class="xs:block">Update</span>
                    </x-elements.button>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Detail Anggota</x-header-form>
            <x-elements.detail label="Nomor Anggota">{{ $this->setIfNull($loadData['masterAnggota']['nomor_anggota'], '-') }}</x-elements.detail>
            <x-elements.detail label="Nama Anggota">{{ $this->setIfNull($loadData['masterAnggota']['nama'],'-') }}</x-elements.detail>
            <x-elements.header-form>Detail Tagihan</x-header-form>
            <x-elements.detail label="Periode">{{ $this->toMonth($loadData['bulan']) }} {{$loadData['tahun']}}</x-elements.detail>
            <x-elements.detail label="Uraian">{{ $this->setIfNull($loadData['uraian'], '-') }}</x-elements.detail>
            <x-elements.detail label="Jumlah">{{ 'Rp. '.$this->toRupiah($loadData['jumlah_tagihan']) }}</x-elements.detail>
            <x-elements.detail label="Remarks">{{ $this->setIfNull($loadData['remarks'], '-') }}</x-elements.detail>
            @if(isset($loadData['t_pinjaman_id']))
            <x-elements.header-form>Detail Pinjaman</x-header-form>
            <x-elements.detail label="Nomor Pinjaman">{{ $this->setIfNull($loadData['pinjamanAnggota']['nomor_pinjaman'], '-') }}</x-elements.detail>
            <x-elements.detail label="Jenis Pinjaman">{{ $this->setIfNull($loadData['pinjamanAnggota']['masterJenisPinjaman']['nama'], '-') }}</x-elements.detail>
            <x-elements.detail label="Jumlah Pinjaman">{{ 'Rp. '.$this->toRupiah($loadData['pinjamanAnggota']['ri_jumlah_pinjaman'], '-') }}</x-elements.detail>
            @endif
            <x-elements.header-form>Detail Pembayaran</x-header-form>
            <x-elements.detail label="Tgl Jatuh Tempo">{{ $loadData['tgl_jatuh_tempo'] ? date('Y-m-d', strtotime($this->loadData['tgl_jatuh_tempo'])) : '-' }}</x-elements.detail>
            <x-elements.detail label="Status Pembayaran">{{ $loadData['p_status_pembayaran_id'] ? $loadData['statusPembayaran']['status_name'] : '-' }}</x-elements.detail>
            <x-elements.detail label="Tanggal Dibayar">{{ $loadData['paid_at'] ? date('Y-m-d', strtotime($this->loadData['paid_at'])) : '-' }}</x-elements.detail>
            <x-elements.detail label="Jumlah Pembayaran">{{ 'Rp. '.$this->toRupiah($loadData['jumlah_pembayaran'], '-') }}</x-elements.detail>
            <x-elements.detail label="Status Pembayaran">{{ $loadData['p_metode_pembayaran_id'] ? $loadData['metodePembayaran']['metode_name'] : '-' }}</x-elements.detail>
        </div>
    </div>
</div>
