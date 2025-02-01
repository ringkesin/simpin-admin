@props([
    'label' => 'Sub Dropdown Title',
    'links'
])
<div class="relative w-full group">
    <div class="flex cursor-pointer select-none items-center rounded gap-x-1.5 py-1 px-2.5 text-gray-800 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-800 outline-none">
        {{ $label }}
        <x-ph-caret-right class='!me-0 w-3 h-3 ml-auto' />
    </div>
    <div data-submenu class="absolute top-0 right-0 invisible mr-1 duration-200 ease-out translate-x-full opacity-0 group-hover:mr-0 group-hover:visible group-hover:opacity-100">
        <div class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white border-gray-300 dark:bg-slate-900 dark:border-slate-700/50 p-2 shadow-sm  animate-in slide-in-from-left-1 w-48">
            {{ $links }}
        </div>
    </div>
</div>