@props([
    'name'
])
<select
    {{ $attributes->class(['cursor-pointer block w-full px-3 py-0.5 text-sm border-gray-200 rounded-md pe-9
    focus:border-[#658cff] focus:ring-white
    disabled:opacity-50 disabled:pointer-events-none
    bg-white
    border-gray-300
    text-gray-800
    placeholder-gray-400']) }}
    @isset($name) name="{{ $name }}" @endif
    >
    {{ $slot }}
</select>
{{-- Tanda Seru untuk Error --}}
@if ($errors->has($name))
<div {{ $attributes->class(["w-auto px-3 py-1 text-xs text-center text-white rounded group-hover:block bg-rose-500 top-full"])}}>
    {{ $errors->first($name) }}
</div>
@endif
