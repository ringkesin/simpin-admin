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
            <div class="grid w-full grid-cols-1 gap-4 px-5 py-5 text-xl font-bold place-items-center">
                <span>Detail</span>
            </div>
            <hr class="px-5 py-5">
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Nomor Anggota -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="pinjaman">
                               Jumlah Pinjaman
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">

                          Rp.   {{$this->toRupiah($loadData['pinjaman'])}}
                        </div>
                    </div>
                    <!-- Group Input Nama -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nama">
                               Bunga
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['margin']}} %
                        </div>
                    </div>
                    <!-- Group Input NIK -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nik">
                                Tahun
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['tahun_margin']}}
                        </div>
                    </div>
                    <!-- Group Input KTP -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                           &nbsp;
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            &nbsp;
                        </div>
                    </div>
                    <!-- Group Input Alamat -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="alamat">
                                &nbsp;
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            &nbsp;
                        </div>
                    </div>
                    <!-- Group Input Tanggal Lahir -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            &nbsp;
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            &nbsp;
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tanggal_masuk">
                                Tenor <span class="text-red-500">: </span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['tenor']}} Bulan
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_from">
                               Angsuran <span class="text-red-500">:</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                          Rp.  {{$this->toRupiah($loadData['angsuran'])}}
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            &nbsp;
                        <div class="col-span-12 md:col-span-8">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
