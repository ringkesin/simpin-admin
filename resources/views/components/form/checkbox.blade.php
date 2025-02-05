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
        {{ $attributes->class(['shrink-0 mt-0.5 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-slate-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800']) }} 
        id="{{ $id }}"
        @isset($name) name="{{ $name }}" @endif 
        @isset($value) value="{{ $value }}" @endif 
    >
    @isset($label) 
    <label for="{{ $id }}" class="text-sm text-gray-600 ms-2 dark:text-slate-300">{{ $label }}</label>
    @endif
</div>