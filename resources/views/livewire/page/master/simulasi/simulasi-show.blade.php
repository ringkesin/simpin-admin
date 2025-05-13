<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
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
                    <x-elements.button wire:navigate :href="route('master.simulasi.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
                <div>

                    <x-elements.button wire:navigate :href="route('master.simulasi.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
                        <x-lucide-square-pen class="size-5"/>
                        <span class="xs:block">Update</span>
                    </x-elements.button>


                    {{-- <x-elements.button-href
                        :href="route('master.anggota.destroy', $attr['data']['id'])"
                        button-type="{{ 'danger' }}"
                        class='confirm-delete'
                    >
                        <i class="fa-regular fa-trash-can"></i>
                        <span class="hidden ml-2 xs:block">Delete</span>
                    </x-elements.button-href> --}}
                </div>
            </div>
        </div>
        <div class="mt-5">
            <x-elements.header-form>Detail Simulasi</x-header-form>
            <x-elements.detail label="Jenis Pinjaman">{{$loadData['JenisPinjaman']['nama']}}</x-elements.detail>
            <x-elements.detail label="Tahun">{{$loadData['tahun_margin']}}</x-elements.detail>
            <x-elements.detail label="Tenor">{{$loadData['tenor']}} Bulan</x-elements.detail>
            <x-elements.detail label="Margin/Bunga">{{$loadData['margin']}}%</x-elements.detail>
            <x-elements.detail label="Biaya Admin">{{$loadData['biaya_admin']}}%</x-elements.detail>
        </div>
    </div>
</div>
