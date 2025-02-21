@props([
    'value',
    'name',
    'placeholder',
    'icon',
])

@php
    $class = 'block px-3 py-0.5 text-sm rounded-md disabled:opacity-50 disabled:pointer-events-none ';
    $class .= 'border-gray-300 dark:border-slate-700 ';
    $class .= 'text-gray-800 dark:text-slate-300 ';
    $class .= 'focus:border-[#658cff] dark:focus:border-[#658cff] focus:ring-white dark:focus:ring-slate-900 ';
    $class .= 'bg-white dark:bg-slate-900 ';
    $class .= 'placeholder-gray-400 dark:placeholder-slate-500 ';
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
    <textarea {{ $attributes->class([$class]) }}
        @isset($name) name="{{ $name }}" @endif
        @isset($value) value="{{ $value }}" @endif
        @isset($placeholder) placeholder="{{ $placeholder }}" @endif
        autocomplete="off"></textarea>

    {{-- Tanda Seru untuk Error --}}
    @if ($errors->has($name))
        <div class="w-auto px-3 py-1 text-xs text-center text-white rounded group-hover:block bg-rose-500 top-full">
            {{ $errors->first($name) }}
        </div>
    @endif
</div>
