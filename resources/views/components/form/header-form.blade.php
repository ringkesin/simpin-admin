<header {!! $attributes->merge(['class' => 'py-4 mb-5 border-b border-slate-100 dark:border-slate-700']) !!}>
    <h2 class="font-semibold text-slate-800 dark:text-slate-100">
        {{ $slot }}
    </h2>
</header>