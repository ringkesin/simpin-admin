@props([
    'value',
    'name',
    'placeholder',
    'icon',
])

@php
    $class = 'block px-3 py-0.5 text-sm rounded-md disabled:opacity-50 disabled:pointer-events-none ';
    $class .= 'border border-gray-300 ';
    $class .= 'text-gray-800 ';
    $class .= 'focus:border-[#658cff] focus:ring-white ';
    $class .= 'bg-white ';
    $class .= 'placeholder-gray-400 ';
    if (isset($icon)) {
        $class .= 'ps-10 ';
    }
    if ($errors->has($name)) {
        $class .= 'border-rose-500 focus:border-rose-500 focus:ring-rose-500 pe-10 '; // Tambahkan padding untuk tanda seru
    }
@endphp

<div class="relative">
    {{-- Input Field --}}
    {{$slot}}
    <input {{ $attributes->class([$class]) }}
        @isset($name) name="{{ $name }}" @endif
        @isset($value) value="{{ $value }}" @endif
        @isset($placeholder) placeholder="{{ $placeholder }}" @endif
        autocomplete="off"
        type="text" />

    {{-- Tanda Seru untuk Error --}}
    @if ($errors->has($name))
        <div class="absolute inset-y-0 flex items-center w-auto cursor-pointer end-0 pe-3">
            <div class="relative group">
                {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 cursor-pointer text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M6.938 4h10.124C18.69 4 19.25 4.56 19.25 5.25v13.5c0 .69-.56 1.25-1.25 1.25H6.938c-.69 0-1.25-.56-1.25-1.25V5.25c0-.69.56-1.25 1.25-1.25z" />
                </svg> --}}
                <x-ph-warning-circle class="text-red-500"/>
                {{-- Tooltip Error --}}
                <div class="absolute z-10 hidden w-auto px-4 py-2 mt-1 text-xs text-center text-white -translate-x-1/2 rounded group-hover:block bg-rose-500 left-1/2 top-full">
                    {{ $errors->first($name) }}
                </div>

            </div>
        </div>
    @endif
</div>
