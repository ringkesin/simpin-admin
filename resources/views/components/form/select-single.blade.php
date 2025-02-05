@props([
    'name'
])
<select
    {{ $attributes->class(['cursor-pointer block w-full px-3 py-0.5 text-sm border-gray-200 rounded-md pe-9 
    focus:border-[#658cff] dark:focus:border-[#658cff] focus:ring-white dark:focus:ring-slate-900 
    disabled:opacity-50 disabled:pointer-events-none 
    bg-white dark:bg-slate-900 
    border-gray-300 dark:border-slate-700 
    text-gray-800 dark:text-slate-300 
    placeholder-gray-400 dark:placeholder-slate-500']) }}
    @isset($name) name="{{ $name }}" @endif 
    >
    {{ $slot }}
</select>