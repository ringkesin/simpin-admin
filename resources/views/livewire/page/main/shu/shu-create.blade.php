<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
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
            </div>
        </div>
        <form wire:submit="saveInsert">
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
                                    @foreach ($this->loadData as $arrayLoop)
                                        <option value="{{ $arrayLoop['p_anggota_id'] }}">{{ $arrayLoop['nomor_anggota'] }} - {{ $arrayLoop['nama'] }}</option>
                                    @endforeach
                            </x-form.select2-single>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------Period Section------------------------>
            <x-elements.header-form>Periode</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Tahun -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="tahun">
                                Tahun <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select-single name="tahun" wire:model.lazy="tahun" class="w-full md:w-2/4" id="tahun">
                                <option value="">Pilih Tahun</option>
                                @foreach($this->getYearRange(5) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </x-form.select-single>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------SHU Section------------------------>
            <x-elements.header-form>SHU Detail</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group SHU Diterima -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="shu_diterima">
                                SHU Diterima <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="shu_diterima" wire:model.lazy="shu_diterima"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kolom Tengah -->
                <div class="hidden col-span-12 md:col-span-6">
                    <!-- Group SHU Dibagi -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="shu_dibagi">
                                SHU Dibagi <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="shu_dibagi" wire:model.lazy="shu_dibagi"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="hidden col-span-12 md:col-span-6">
                    <!-- Group SHU Ditabung -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="shu_ditabung">
                                SHU Ditabung <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="shu_ditabung" wire:model.lazy="shu_ditabung"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="hidden col-span-12 md:col-span-6">
                    <!-- Group SHU Tahun Lalu -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="shu_tahun_lalu">
                                SHU Tahun Lalu <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="hidden col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="number" name="shu_tahun_lalu" wire:model.lazy="shu_tahun_lalu"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-elements.header-form></x-elements.header-form>
            <div class="grid grid-cols-12 gap-10">
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
