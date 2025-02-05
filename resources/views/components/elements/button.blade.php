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
            $class .= 'text-white dark:text-white bg-[#658cff] dark:bg-[#658cff] hover:bg-white dark:hover:bg-slate-900 border-[#658cff] hover:text-[#658cff] dark:hover:text-slate-300 '; 
        }
        if($style == 'outlined'){
            $class .= 'text-[#658cff] dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-[#658cff] dark:hover:bg-[#658cff] border-[#658cff] hover:text-white dark:hover:text-white '; 
        }
    }

    if($variant == 'secondary'){
        if($style == 'solid'){
            $class .= 'text-gray-800 dark:text-slate-300 bg-gray-200 dark:bg-slate-800 hover:bg-white dark:hover:bg-slate-900 border-gray-200 hover:border-gray-300 dark:border-slate-800 dark:hover:border-slate-700 hover:text-gray-800 dark:hover:text-slate-300 '; 
        }
        if($style == 'outlined'){
            $class .= 'text-gray-800 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-gray-100 dark:hover:bg-slate-800 border-gray-300 dark:border-slate-700 hover:text-gray-800 dark:hover:text-slate-300 '; 
        }
    }

    if($variant == 'success'){
        if($style == 'solid'){
            $class .= 'text-white dark:text-white bg-teal-400 dark:bg-teal-500 hover:bg-white dark:hover:bg-slate-900 border-teal-400 dark:hover:border-teal-500 hover:text-teal-400 dark:hover:text-slate-300 '; 
        }
        if($style == 'outlined'){
            $class .= 'text-teal-400 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-teal-400 dark:hover:bg-teal-500 border-teal-400 dark:hover:border-teal-500 hover:text-white dark:hover:text-white '; 
        }
    }

    if($variant == 'warning'){
        if($style == 'solid'){
            $class .= 'text-white dark:text-white bg-yellow-400 dark:bg-yellow-500 hover:bg-white dark:hover:bg-slate-900 border-yellow-400 dark:hover:border-yellow-500 hover:text-yellow-400 dark:hover:text-slate-300 '; 
        }
        if($style == 'outlined'){
            $class .= 'text-gray-800 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-yellow-400 dark:hover:bg-yellow-500 border-yellow-400 dark:hover:border-yellow-500 hover:text-white dark:hover:text-white '; 
        }
    }

    if($variant == 'info'){
        if($style == 'solid'){
            $class .= 'text-white dark:text-white bg-sky-400 dark:bg-sky-500 hover:bg-white dark:hover:bg-slate-900 border-sky-400 dark:hover:border-sky-500 hover:text-sky-400 dark:hover:text-slate-300 '; 
        }
        if($style == 'outlined'){
            $class .= 'text-gray-800 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-sky-400 dark:hover:bg-sky-500 border-sky-400 dark:hover:border-sky-500 hover:text-white dark:hover:text-white '; 
        }
    }

    if($variant == 'danger'){
        if($style == 'solid'){
            $class .= 'text-white dark:text-white bg-[#f1416c] dark:bg-rose-500 hover:bg-white dark:hover:bg-slate-900 border-[#f1416c] hover:border-[#f1416c] dark:border-rose-500 dark:hover:border-rose-500 hover:text-rose-500 dark:hover:text-slate-300 '; 
        }
        if($style == 'outlined'){
            $class .= 'text-gray-800 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-[#f1416c] dark:hover:bg-rose-500 border-[#f1416c] dark:hover:border-rose-500 hover:text-white dark:hover:text-white '; 
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