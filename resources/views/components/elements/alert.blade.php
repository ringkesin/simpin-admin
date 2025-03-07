@props([
    'type' => 'secondary'
])

<?php
$class = 'p-4 text-sm border rounded-lg';
if($type == 'primary'){
    $class .= '
        bg-blue-50 
        text-[#658cff] 
        border-[#658cff]
    ';
}

if($type == 'secondary'){
    $class .= '
        bg-gray-100 
        text-gray-800 
        border-gray-500
    ';
}

if($type == 'success'){
    $class .= '
        bg-teal-100 
        text-teal-800 
        border-teal-500
    ';
}

if($type == 'warning'){
    $class .= '
        bg-amber-50 
        text-amber-800 
        border-amber-400 
    ';
}

if($type == 'info'){
    $class .= '
        bg-sky-100 
        text-sky-800 
        border-sky-500
    ';
}

if($type == 'danger'){
    $class .= '
        bg-rose-100 
        text-rose-800 
        border-rose-500
    ';
}
?>
<div {{ $attributes->class([$class]) }} role="alert" tabindex="-1" aria-labelledby="hs-with-description-label">
    <div class="flex items-center">
        <div class="shrink-0">
            {{ $icon }}
        </div>
        <div class="ms-4">
            <h3 id="hs-with-description-label" class="text-sm font-semibold">
                {{ $title }}
            </h3>
            <div class="text-sm">
                {{ $description }}
            </div>
        </div>
    </div>
</div>