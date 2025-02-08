<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="grid grid-cols-2 mb-6 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
        <div>
            <div class="flex justify-end">
                <div>
                    <x-elements.button :href="url('master/asuransi/create')" button-type="{{ 'primary' }}">
                        <x-lucide-plus class="size-3"/>
                        <span class="xs:block">Tambah </span>
                    </x-elements.button>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">

    </div>
</div>
