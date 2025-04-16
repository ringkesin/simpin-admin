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
                    <x-elements.button wire:navigate :href="route('main.shu.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
                <div>
                    <x-elements.button wire:navigate :href="route('main.shu.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
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
            <x-elements.header-form>Detail SHU</x-header-form>
            <x-elements.detail label="Periode">{{$loadData['tahun']}}</x-elements.detail>
            <x-elements.detail label="SHU Diterima">{{ 'Rp. '.$this->toRupiah($loadData['shu_diterima']) }}</x-elements.detail>
            <x-elements.detail label="SHU Dibagi">{{ 'Rp. '.$this->toRupiah($loadData['shu_dibagi']) }}</x-elements.detail>
            <x-elements.detail label="SHU Ditabung">{{ 'Rp. '.$this->toRupiah($loadData['shu_ditabung']) }}</x-elements.detail>
            <x-elements.detail label="SHU Tahun Lalu">{{ 'Rp. '.$this->toRupiah($loadData['shu_tahun_lalu']) }}</x-elements.detail>
        </div>
    </div>
</div>
