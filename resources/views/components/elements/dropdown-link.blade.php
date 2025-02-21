<a {{ $attributes->class(['
    relative flex justify-normal w-full cursor-pointer select-none group
    items-center rounded gap-x-1.5 py-1 px-2.5 hover:bg-gray-200 text-gray-800 outline-none
    data-[disabled]:opacity-50 data-[disabled]:pointer-events-none']) }} >
    {{ $slot }}
</a>
