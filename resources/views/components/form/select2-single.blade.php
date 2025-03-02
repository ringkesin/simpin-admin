@props([
    'id' => '',
    'name' => '',
    'value' => '',
    'disabled' => false,
    'onchanged' => false
])

@php
    $errorClass = $errors->has($name) ? '!border-rose-500 !focus:border-rose-500 !focus:ring-rose-500' : '!border-gray-200';
@endphp

<div class="relative">
    <div x-data x-init="
        // Inisialisasi Select2
        $nextTick(() => {
            let selectEl = $('#{{ $id }}');
            if ('{{ $value }}' !== '') {
                selectEl.val('{{ $value }}').trigger('change');
            }
            selectEl.select2({
                theme: 'default',
                dropdownCssClass: 'cursor-pointer w-full py-3 px-4 text-sm border-gray-200 overflow-hidden rounded-lg',
            }).on('select2:select', function (e) {
                if('{{$onchanged}}') {
                    @this.call('{{$onchanged}}', e.target.value);
                    {{-- console.log('{{$onchanged}}'); --}}
                }
                @this.set('{{ $name }}', e.target.value);
            });

            // Tambahkan class ke elemen Select2 untuk styling
            $('.select2-container').addClass('!w-full');
            $('.select2-selection').addClass('!h-auto !cursor-pointer !w-full !px-3 !py-0.5 !text-sm {{$errorClass = $errors->has($name) ? '!border-rose-500 !focus:border-rose-500 !focus:ring-rose-500' : '!border-gray-200'}} !overflow-hidden !rounded-lg !pe-9 !focus:border-[#658cff] !dark:focus:border-[#658cff] !focus:ring-white !dark:focus:ring-slate-900 !disabled:opacity-50 !disabled:pointer-events-none !bg-white !dark:bg-slate-900 !dark:border-slate-700 !text-gray-800 !dark:text-slate-300 !placeholder-gray-400 !dark:placeholder-slate-500');
            $('.select2-selection__arrow').addClass('!flex !items-center !justify-center');
        });
    " wire:ignore {{ $attributes->merge(['class' => 'flex items-center rounded-lg overflow-hidden shadow-sm' . $errorClass ]) }}>
        {{-- Select Dropdown --}}
        <select
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'single-select select2']) }}
            @isset($name) name="{{ $name }}" @endif
            @if ($id != '') id="{{ $id }}" @endif>
            {{ $slot }}
        </select>
    </div>

    {{-- Error Tooltip --}}
    @if ($errors->has($name))
        <div {{ $attributes->class(["w-auto px-3 py-1 text-xs text-center text-white rounded group-hover:block bg-rose-500 top-full"])}}>
            {{ $errors->first($name) }}
        </div>
    @endif
</div>
