<div class="grid mb-3 md:grid-cols-3 sm:grid-flow-row">
    <div class="px-3 text-sm font-medium md:col-span-1 md:text-right sm:text-left text-slate-500">{!! $label !!} {!! isset($required) ? '<span class="text-red-500">*</span>' : '' !!}</div>
    <div class="px-3 text-sm font-medium md:col-span-2 text-slate-800 dark:text-slate-100">{{ $slot }}</div>
</div>
