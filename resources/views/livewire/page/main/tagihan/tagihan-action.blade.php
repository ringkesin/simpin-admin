<div class='flex items-center gap-2'>
    <x-elements.button-href :href="route('main.tagihan.show', $row['t_tagihan_id'])" button-type="{{ 'secondary' }}" class="px-2.5 py-1 rounded-md shadow-md border-slate-200">
        <x-lucide-eye class="size-4"/>
    </x-elements.button-href>
</div>
