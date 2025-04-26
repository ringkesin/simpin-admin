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
        <div class='px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200'>
            <div class="flex justify-between gap-2">
                <div>
                    <x-elements.button wire:navigate :href="route('main.konten.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
                <div>
                    <x-elements.button wire:navigate :href="route('main.konten.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
                        <x-lucide-square-pen class="size-5"/>
                        <span class="xs:block">Update</span>
                    </x-elements.button>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <x-elements.detail label="Judul Konten">{{ $this->setIfNull($loadData['content_title'], '-') }}</x-elements.detail>
            <x-elements.detail label="Tipe Konten">{{ $this->setIfNull($loadData['contentType']['content_name'], '-') }}</x-elements.detail>
            <x-elements.detail label="Valid Dari">{{ $this->setIfNull($loadData['valid_from'], '-') }}</x-elements.detail>
            <x-elements.detail label="Valid Sampai">{{ $this->setIfNull($loadData['valid_to'], '-') }}</x-elements.detail>
            <x-elements.header-form>Isi Konten</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <div class="col-span-12 rounded-lg shadow-md md:col-span-12 bg-slate-100">
                    <div class="flex justify-center pt-5">
                        <img src="{{ $loadData['thumbnail_path'] }}" class="w-1/2 rounded-lg">
                    </div>
                    <div class="flex justify-center mt-5">
                        <div class="p-5 prose">
                            {!! $loadData['content_text'] !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
