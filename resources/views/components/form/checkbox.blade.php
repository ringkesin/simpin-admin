@props([
    'value',
    'name',
    'label',
    'id'
])

@php
if( ! isset($id)){
    $id = uniqid();
}
@endphp

<div class="flex">
    <input
        type="checkbox"
        {{ $attributes->class(['shrink-0 mt-0.5 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none']) }}
        id="{{ $id }}"
        @isset($name) name="{{ $name }}" @endif
        @isset($value) value="{{ $value }}" @endif
        @if($value == 1) checked @endif
    >
    @isset($label)
    <label for="{{ $id }}" class="text-sm text-gray-600 ms-2">{{ $label }}</label>
    @endif
</div>
