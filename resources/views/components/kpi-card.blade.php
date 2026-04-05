@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
    $colors = [
        'blue' => 'bg-blue-100',
        'green' => 'bg-green-100',
        'yellow' => 'bg-yellow-100',
        'purple' => 'bg-purple-100',
        'red' => 'bg-red-100',
    ];
    
    $iconColors = [
        'blue' => 'text-blue-500',
        'green' => 'text-green-500',
        'yellow' => 'text-yellow-500',
        'purple' => 'text-purple-500',
        'red' => 'text-red-500',
    ];
    
    $bgColor = $colors[$color] ?? $colors['blue'];
    $iconColor = $iconColors[$color] ?? $iconColors['blue'];
@endphp

<div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-lg transition">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs sm:text-sm text-gray-600 mb-1 truncate">{{ $title }}</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800 truncate">{{ $value }}</p>
        </div>
        <div class="w-12 h-12 sm:w-16 sm:h-16 {{ $bgColor }} rounded-full flex items-center justify-center flex-shrink-0 ml-2">
            <i class="fas {{ $icon }} text-lg sm:text-2xl {{ $iconColor }}"></i>
        </div>
    </div>
</div>