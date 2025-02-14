<div class='flex items-center gap-2'>
    {{-- <div class='w-9'>{{ $row['id'] }}</div> --}}
    {{-- <div> --}}
        <x-elements.button-href :href="route('user.show', $row['id'])" button-type="{{ 'secondary' }}" class="px-2 rounded-lg shadow-sm border-slate-200">
            {{-- <i class="fa-regular fa-eye"></i> --}}
            <x-ph-eye-duotone class="size-5"/>
        </x-elements.button-href>
    {{-- </div> --}}
        <x-elements.button-href :href="route('user.edit', $row['id'])" button-type="{{ 'secondary' }}" class="border-slate-200 px-2.5 rounded-lg shadow-sm">
            {{-- <i class="fa-regular fa-pen-to-square"></i> --}}<x-ph-pencil-duotone class="size-5"/>
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
