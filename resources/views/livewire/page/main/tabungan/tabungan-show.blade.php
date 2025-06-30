<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
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
                <div>
                    <x-elements.button wire:navigate :href="route('main.tabungan.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
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
            <x-elements.header-form>Detail Tabungan</x-header-form>
            <x-elements.detail label="Periode">{{ $this->toMonth($loadData['bulan']) }} {{$loadData['tahun']}}</x-elements.detail>
            <x-elements.detail label="Simpanan Pokok">{{ 'Rp. '.$this->toRupiah($loadData['simpanan_pokok']) }}</x-elements.detail>
            <x-elements.detail label="Simpanan Wajib">{{ 'Rp. '.$this->toRupiah($loadData['simpanan_wajib']) }}</x-elements.detail>
            <x-elements.detail label="Tabungan Sukarela">{{ 'Rp. '.$this->toRupiah($loadData['tabungan_sukarela']) }}</x-elements.detail>
            <x-elements.detail label="Tabungan Indir">{{ 'Rp. '.$this->toRupiah($loadData['tabungan_indir']) }}</x-elements.detail>
        </div>
    </div>
</div>
