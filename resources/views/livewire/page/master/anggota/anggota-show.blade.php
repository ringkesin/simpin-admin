<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div class="grid grid-cols-2 mb-6 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800 dark:text-slate-100">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-sm shadow-lg dark:bg-slate-800 border-slate-200 dark:border-slate-700">
        <!----------------Action Button------------------------>
        <div class='px-4 py-4 mb-6 border rounded bg-slate-50 dark:bg-slate-700/25 border-slate-200 dark:border-slate-600/75'>
            <div class="flex justify-between gap-2">
                <div>
                    <x-elements.button :href="route('master.anggota.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
                <div>
                    <x-elements.button :href="route('master.anggota.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
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
                            <x-form.label for="p_anggota_id">
                                Nomor Anggota
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['p_anggota_id']}}
                        </div>
                    </div>
                    <!-- Group Input Nama -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nama">
                                Nama Anggota
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['nama']}}
                        </div>
                    </div>
                    <!-- Group Input NIK -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nik">
                                NIK Anggota
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['nik']}}
                        </div>
                    </div>
                    <!-- Group Input KTP -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="ktp">
                                No KTP
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['ktp']}}
                        </div>
                    </div>
                    <!-- Group Input Alamat -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="alamat">
                                Alamat
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['alamat']}}
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tanggal_masuk">
                                Tanggal Masuk <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['tanggal_masuk']}}
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_from">
                                Valid Dari <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['valid_from']}}
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_to">
                                Valid Sampai
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            {{$loadData['valid_to']}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
