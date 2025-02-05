@if ($buttonType == 'primary')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn bg-green-500 hover:bg-green-600 text-white']) }}>
        {{ $slot }}
    </a>
@elseif ($buttonType == 'secondary')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn dark:bg-slate-800 border border-green-500 hover:border-green-500 dark:border-slate-700 text-green-500 hover:bg-green-500 hover:text-white dark:border-green-500 dark:hover:bg-green-500 dark:hover:border-green-500']) }}>
        {{ $slot }}
    </a>
@elseif ($buttonType == 'danger')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn dark:bg-slate-800 border border-rose-500 hover:border-rose-500 dark:border-slate-700 text-rose-500 hover:bg-rose-500 hover:text-white dark:border-rose-500 dark:hover:bg-rose-500 dark:hover:border-rose-500']) }}>
        {{ $slot }}
</a>
@elseif ($buttonType == 'info')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn dark:bg-slate-800 hover:border-indigo-500 dark:border-slate-700 text-indigo-500 hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-500 dark:hover:border-indigo-500']) }}>
        {{ $slot }}
</a>
@else
    {{-- Other button --}}
@endif
