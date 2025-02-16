@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-thin mb-1']) }}>
    {{ $value ?? $slot }}
</label>
