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
                    <x-elements.button :href="route('master.anggota.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <form wire:submit="saveInsert">
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Nomor Anggota -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nomor_anggota">
                                Nomor Anggota <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="nomor_anggota" wire:model.lazy="nomor_anggota"/>
                        </div>
                    </div>
                    <!-- Group Input Nama -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nama">
                                Nama Anggota <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="nama" wire:model.lazy="nama"/>
                        </div>
                    </div>
                    <!-- Group Input Email -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="email">
                                Email
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="email" name="email" wire:model.lazy="email"/>
                        </div>
                    </div>
                    <!-- Group Input Mobile -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="mobile">
                                No HP
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="number" name="mobile" wire:model.lazy="mobile"/>
                        </div>
                    </div>
                    <!-- Group Input NIK -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="nik">
                                NIK Anggota <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="nik" wire:model.lazy="nik"/>
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
                            <x-form.input class="w-full" type="text" name="ktp" wire:model.lazy="ktp"/>
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
                            <x-form.input class="w-full" type="text" name="alamat" wire:model.lazy="alamat"/>
                        </div>
                    </div>
                    <!-- Group Input Tanggal Lahir -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tgl_lahir">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" id="tgl_lahir" name="tgl_lahir" :value="''" wire:model.lazy="tgl_lahir"/>
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
                            <x-form.input class="w-full" type="date" id="tanggal_masuk" name="tanggal_masuk" :value="''" wire:model.lazy="tanggal_masuk"/>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_from">
                                Valid Dari <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" id="valid_from" name="valid_from" :value="''" wire:model.lazy="valid_from"/>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_to">
                                Valid Sampai
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" id="valid_to" name="valid_to" :value="''" wire:model.lazy="valid_to"/>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="is_registered">
                                Member ?
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.checkbox class="w-8 py-3" :id="'is_registered'" :name="'is_registered'" value="" wire:model.lazy="is_registered"/>
                        </div>
                    </div>
                </div>
            </div>
            <x-elements.button-submit class="mt-5" wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
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
                    <x-ph-paper-plane-tilt-duotone class="size-5"/>
                    <span class="xs:block">
                        Save data
                    </span>
                </div>
            </x-elements.button-submit>
        </form>
    </div>
</div>
