@props([
    'breadcrumbs' => null
])

@isset($breadcrumbs)
    <ol class="flex items-center whitespace-nowrap">
        {{-- <li><x-ph-tag-chevron class='mt-[-1.5px]' /></li> --}}
        @foreach ($breadcrumbs as $breadcrumb)
            <li class="flex items-center text-sm">
                <a class="
                    flex items-center
                    text-gray-800
                    dark:text-slate-300
                    hover:text-blue-600
                    dark:hover:text-blue-600
                    focus:outline-none
                    focus:text-blue-600
                    @if($loop->last) font-semibold @endif"
                    href="{{ $breadcrumb['link'] }}" wire:navigate>
                    {{ $breadcrumb['label'] }}
                </a>

                @if(!$loop->last)
                <svg class="flex-shrink-0 w-5 h-5 mx-1 text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 13L10 3" stroke="currentColor" stroke-linecap="round" />
                </svg>
                @endif
            </li>
        @endforeach
    </ol>
@endisset
