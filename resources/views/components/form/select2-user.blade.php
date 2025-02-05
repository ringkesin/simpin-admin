@props([
    'id' => '',
    'name' => '',
    'value' => '',
    'disabled' => false
])

@php
    $errorClass = $errors->has($name) ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-gray-200';
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
                placeholder: 'Pilih User',
                ajax: {
                    url: '/components/search-users',  // Ganti dengan URL API pencarian Anda
                    dataType: 'json',
                    delay: 250,  // Waktu delay untuk pengiriman request
                    data: function (params) {
                        return {
                            q: params.term,  // Kirimkan term yang dimasukkan pengguna ke backend
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.p_user_id,
                                    text: item.fullname + ' (' + item.username + ')',
                                };
                            })
                        };
                    },
                }
            }).on('select2:select', function (e) {
                @this.set('{{ $name }}', e.target.value);
            });

            // Tambahkan class ke elemen Select2 untuk styling
            $('.select2-container').addClass('!w-full');
            $('.select2-selection').addClass('!h-auto !cursor-pointer !w-full !py-2 !px-4 !text-sm !border-gray-200 !overflow-hidden !rounded-lg !pe-9 !focus:border-[#658cff] !dark:focus:border-[#658cff] !focus:ring-white !dark:focus:ring-slate-900 !disabled:opacity-50 !disabled:pointer-events-none !bg-white !dark:bg-slate-900 !dark:border-slate-700 !text-gray-800 !dark:text-slate-300 !placeholder-gray-400 !dark:placeholder-slate-500');
            $('.select2-selection__arrow').addClass('!flex !items-center !justify-center');
        });
    " wire:ignore {{ $attributes->merge(['class' => 'flex items-center rounded-lg overflow-hidden shadow-sm']) }}>
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
    <div class="absolute inset-y-0 flex items-center end-0 pe-3 cursor-pointer w-auto">
        <div class="relative group">
            <x-ph-warning-circle class="text-red-500"/>
            <div class="absolute hidden group-hover:block bg-rose-500 text-white text-xs rounded px-4 py-2 left-1/2 -translate-x-1/2 top-full mt-1 z-10 w-auto text-center">
                {{ $errors->first($name) }}
            </div>
        </div>
    </div>
    @endif
</div>
