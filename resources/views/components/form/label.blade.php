@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium mb-1 dark:text-neutral-200']) }}>
    {{ $value ?? $slot }}
</label>
