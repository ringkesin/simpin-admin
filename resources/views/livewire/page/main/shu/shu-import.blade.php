<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">
        <!----------------Action Button------------------------>
        <div class='px-4 py-4 border rounded bg-slate-50 border-slate-200'>
            <div class="flex justify-between gap-2">
                <div>
                    <x-elements.button wire:navigate :href="route('main.shu.list')" :variant="'primary'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4 mt-5">
        <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">
            <!----------------Action Button------------------------>
            <div class='px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200'>
                <div class="flex justify-between gap-2">
                    <h2 class="font-semibold text-slate-800 dark:text-slate-100">Export SHU</h2>
                </div>
            </div>
            <form wire:submit="exportSHU">
                <!-- Group Tahun -->
                <div class="grid items-center grid-cols-12 gap-4 mb-4">
                    <div class="col-span-12 md:col-span-4">
                        <x-form.label for="tahun">
                            Tahun <span class="text-red-500">*</span>
                        </x-form.label>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <x-form.select-single name="tahun" wire:model.lazy="tahun" class="w-full" id="tahun">
                            <option value="">Pilih Tahun</option>
                            @foreach($this->getYearRange(2) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </x-form.select-single>
                    </div>
                </div>
                <div class="grid justify-items-end">
                    <x-elements.button-submit wire:loading.attr="disabled">
                        <div wire:loading wire:target="exportSHU">
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
                        <div class='flex gap-x-1' wire:loading.remove wire:target="exportSHU">
                            <x-lucide-folder-output class="size-5"/>
                            <span class="xs:block">
                                Export
                            </span>
                        </div>
                    </x-elements.button-submit>
                </div>
            </form>
        </div>
        <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">
            <!----------------Action Button------------------------>
            <div class='px-4 py-4 border rounded bg-slate-50 border-slate-200'>
                <div class="flex justify-between gap-2">
                    <h2 class="font-semibold text-slate-800 dark:text-slate-100">Import SHU</h2>
                    <div>
                        <x-elements.button :href="'#'" :variant="'secondary'" :style="'outlined'" :type="'link'" wire:click='downloadTemplate'>
                            <x-lucide-download class="size-5"/>
                            <span class="xs:block">Download Template Excel</span>
                        </x-elements.button>
                    </div>
                </div>
            </div>
            <div
                class="p-6 mt-5 text-center border-2 border-gray-300 border-dashed cursor-pointer"
                x-data="{ isDragging: false }"
                x-on:dragover.prevent="isDragging = true"
                x-on:dragleave.prevent="isDragging = false"
                x-on:drop.prevent="isDragging = false"
                x-on:drop="
                    let files = event.dataTransfer.files;
                    $wire.upload('files', files);
                "
                x-on:click="document.getElementById('fileInput').click()"
            >
                <input type="file" wire:model="files" class="hidden" id="fileInput">
                <label for="fileInput" class="block text-gray-600 cursor-pointer">
                    <div wire:loading wire:target="files">
                        <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-blue rounded-full" role="status" aria-label="loading">
                            <span class="sr-only">Processing.....</span>
                        </span>
                        <span class="xs:block">
                            Processing
                        </span>
                    </div>
                    <div wire:loading.remove wire:target="files">
                        Seret & Lepaskan file di sini atau klik untuk memilih
                    </div>
                </label>
            </div>

            <!-- Menampilkan daftar nama file yang telah dipilih -->
            @if ($files)
                <div class="grid grid-cols-6 gap-4 mt-4">
                    <div class="col-span-4 col-start-1">
                        <h3 class="font-semibold text-gray-700">File yang Dipilih:</h3>
                        <ul class="list-disc list-inside">
                            <li class="text-gray-600">{{ $files->getClientOriginalName() }}</li>
                            {{-- @foreach ($files as $file)
                                <li class="text-gray-600">{{ $file->getClientOriginalName() }}</li>
                            @endforeach --}}
                        </ul>
                    </div>
                    <div class="col-span-2 col-end-7">
                        <div class="flex justify-end">
                            <x-elements.button-submit wire:loading.attr="disabled" wire:click='uploadFiles'>
                                <div wire:loading wire:target="uploadFiles">
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
                                <div class='flex gap-x-1' wire:loading.remove wire:target="uploadFiles">
                                    <x-lucide-file-up class="size-5"/>
                                    <span class="xs:block">
                                        Upload data
                                    </span>
                                </div>
                            </x-elements.button-submit>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
