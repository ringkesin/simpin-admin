<div class='flex items-center gap-2'>
    {{-- <div class='w-9'>{{ $row['id'] }}</div> --}}
    {{-- <div> --}}
        <x-elements.button-href :href="route('user.show', $row['id'])" button-type="{{ 'secondary' }}" class="px-2.5 py-1 rounded-md shadow-md border-slate-200">
            {{-- <i class="fa-regular fa-eye"></i> --}}
            <x-lucide-eye class="size-4"/>
        </x-elements.button-href>
    {{-- </div> --}}
        <x-elements.button-href :href="route('user.edit', $row['id'])" button-type="{{ 'secondary' }}" class="border-slate-200 px-2.5 rounded-md shadow-md py-1">
            {{-- <i class="fa-regular fa-pen-to-square"></i> --}}<x-lucide-square-pen class="size-4"/>
        </x-elements.button-href>
    {{-- <div>
        <x-form.button-href
            :href="route('user.destroy', $row['id'])"
            button-type="{{ 'danger' }}"
            class="confirm-delete border-slate-200 px-2.5"
        >
            <i class="fa-regular fa-trash-can"></i>
        </x-form.button-href>
    </div> --}}
</div>
