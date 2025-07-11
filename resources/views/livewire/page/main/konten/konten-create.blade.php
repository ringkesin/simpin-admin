<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
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
                    <x-elements.button wire:navigate :href="route('main.konten.list')" :variant="'primary'" :style="'outlined'" :type="'link'">
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
                    <!-- Group Judul Konten -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="content_title">
                                Judul Konten <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="col-span-12 md:col-span-8">
                                <x-form.input class="w-full" type="text" name="content_title" wire:model.lazy="content_title"/>
                            </div>
                        </div>
                    </div>
                    <!-- Group Tipe Konten -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="p_content_type_id">
                                Tipe Konten <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.select2-single name="p_content_type_id" wire:model.lazy="p_content_type_id" class="w-full md:w-full" id="p_content_type_id">
                                <option value="">Pilih Tipe Konten</option>
                                    @foreach ($this->contentType as $arrayLoop)
                                        <option value="{{ $arrayLoop['p_content_type_id'] }}">{{ $arrayLoop['content_name'] }}</option>
                                    @endforeach
                            </x-form.select2-single>
                        </div>
                    </div>
                    <!-- Group Photo Thumbnail -->
                    <div x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"  class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="thumbnail_path">
                                Photo Thumbnail
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="flex gap-5">
                                <div>
                                    <x-form.input type="file" name="thumbnail_path" wire:model.lazy="thumbnail_path" />
                                </div>
                                <div x-show="uploading" class="relative w-full h-4 mt-4 bg-gray-200 rounded-full shadow-inner md:mt-0 md:ml-4">
                                    <div class="absolute top-0 left-0 h-4 bg-blue-500 rounded-full"
                                        x-bind:style="`width: ${progress}%;`"></div>
                                </div>
                                <div x-show="uploading" class="px-2 text-sm text-center text-gray-500">
                                    Uploading... <span x-text="`${progress}%`"></span>
                                </div>
                            </div>
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
                </div>
                <div class="col-span-12 md:col-span-6"></div>
                <div class="col-span-12 md:col-span-12">
                    <div class="col-span-12 md:col-span-4">
                        <x-form.label for="content_text">
                            Isi Konten
                        </x-form.label>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div
                            x-data
                            x-init="
                                ClassicEditor
                                .create($refs.editor, {
                                    toolbar: {
                                        items: [
                                            'heading', '|',
                                            'bold', 'italic', 'link', '|',
                                            'bulletedList', 'numberedList', 'blockQuote', '|',
                                            'insertTable', '|',
                                            'undo', 'redo'
                                        ]
                                    },
                                    removePlugins: [
                                        'Image',
                                        'ImageToolbar',
                                        'ImageUpload',
                                        'ImageCaption',
                                        'ImageStyle',
                                        'MediaEmbed',
                                        'EasyImage',
                                        'CKBox',
                                        'CKFinder',
                                        'ImageInsert'
                                    ]
                                })
                                .then(editor => {
                                    editor.model.document.on('change:data', () => {
                                        $wire.set('content_text', editor.getData());
                                    });
                                });
                            "
                        >
                            <div wire:ignore>
                                <textarea x-ref="editor"></textarea>
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
