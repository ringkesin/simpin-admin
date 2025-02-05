<button {{ $attributes->merge(['class' => 'w-full rounded-full md:w-32 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium md:rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none']) }} >
    {{ $slot }}
</button>
