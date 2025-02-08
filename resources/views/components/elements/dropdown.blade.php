@props([
    'label' => 'Dropdown Title',
    'links'
])

<div x-data="{ dropdownOpen: false }" class="relative w-auto">
    <button @click="dropdownOpen=true" {{ $attributes->class(['inline-flex items-center justify-center py-1 px-2.5 text-sm
        transition-colors border rounded-md focus:outline-none
        text-gray-800
        bg-white hover:bg-gray-100
        border-gray-300
        focus:bg-[#658cff] focus:text-white focus:border-[#658cff] focus:ring-white
        disabled:opacity-50 disabled:pointer-events-none']) }}>
        {{ $label }} <x-ph-caret-down class='!me-0 ms-2 w-3 h-3' />
    </button>
    <div x-show="dropdownOpen"
        @click.away="dropdownOpen=false"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="-translate-y-2"
        x-transition:enter-end="translate-y-0"
        class="absolute top-0 z-10 mt-[20px] min-w-40 max-w-max"
        x-cloak>
        <div class="p-2 mt-3 z-40 text-sm bg-white border border-gray-300 rounded-md shadow-sm">
            {{ $links }}
        </div>
    </div>
</div>
