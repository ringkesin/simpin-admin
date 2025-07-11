@props([
    'type' => 'button',
    'href' => '#',
    'variant' => 'primary',
    'style' => 'solid'
])

<?php
    $class = 'inline-flex items-center px-3 py-1 text-sm border rounded-md shadow-none outline-none cursor-pointer border-1 ring-0 gap-x-2 disabled:opacity-50 disabled:pointer-events-none ';

    if($variant == 'primary'){
        if($style == 'solid'){
            $class .= 'text-white bg-blue-600 hover:bg-white border-blue-600 hover:text-blue-600 ';
        }
        if($style == 'outlined'){
            $class .= 'text-blue-600 bg-white hover:bg-blue-600 border-blue-600 hover:text-white ';
        }
    }

    if($variant == 'secondary'){
        if($style == 'solid'){
            $class .= 'text-slate-800 bg-slate-200 hover:bg-white border-slate-200 hover:border-slate-300 hover:text-slate-800 ';
        }
        if($style == 'outlined'){
            $class .= 'text-slate-800 bg-white hover:bg-slate-100 border-slate-300 hover:text-slate-800 ';
        }
    }

    if($variant == 'success'){
        if($style == 'solid'){
            $class .= 'text-white bg-green-600 hover:bg-white border-green-600 hover:text-green-600 ';
        }
        if($style == 'outlined'){
            $class .= 'text-green-600 bg-white hover:bg-green-600 border-green-600 hover:text-white ';
        }
    }

    if($variant == 'warning'){
        if($style == 'solid'){
            $class .= 'text-white bg-yellow-400 hover:bg-white border-yellow-400 hover:text-yellow-400 ';
        }
        if($style == 'outlined'){
            $class .= 'text-yellow-400 bg-white hover:bg-yellow-400 border-yellow-400 hover:text-white ';
        }
    }

    if($variant == 'info'){
        if($style == 'solid'){
            $class .= 'text-white bg-sky-400 hover:bg-white border-sky-400 hover:text-sky-400 ';
        }
        if($style == 'outlined'){
            $class .= 'text-sky-400 bg-white hover:bg-sky-400 border-sky-400 hover:text-white ';
        }
    }

    if($variant == 'danger'){
        if($style == 'solid'){
            $class .= 'text-white bg-[#f1416c] hover:bg-white border-[#f1416c] hover:border-[#f1416c] hover:text-rose-500 ';
        }
        if($style == 'outlined'){
            $class .= 'text-gray-800 bg-white hover:bg-[#f1416c] border-[#f1416c] hover:text-white ';
        }
    }
?>

@if($type == 'button')
    <button type="button" {{ $attributes->class([$class]) }}>
        {{ $slot }}
    </button>
@endif

@if($type == 'link')
    <a href="{{ $href }}" {{ $attributes->class([$class]) }}>
        {{ $slot }}
    </a>
@endif
