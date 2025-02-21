@if ($buttonType == 'primary')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn bg-green-500 hover:bg-green-600 text-white']) }}>
        {{ $slot }}
    </a>
@elseif ($buttonType == 'secondary')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn border border-green-500 hover:border-green-500 text-green-500 hover:bg-green-500 hover:text-white']) }}>
        {{ $slot }}
    </a>
@elseif ($buttonType == 'danger')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn border border-rose-500 hover:border-rose-500 text-rose-500 hover:bg-rose-500 hover:text-white']) }}>
        {{ $slot }}
</a>
@elseif ($buttonType == 'info')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn hover:border-indigo-500 text-indigo-500 hover:bg-indigo-500 hover:text-white']) }}>
        {{ $slot }}
</a>
@else
    {{-- Other button --}}
@endif
