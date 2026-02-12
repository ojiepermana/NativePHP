@props(['class' => 'w-64 h-64'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- Background Circle -->
    <circle cx="200" cy="150" r="100" fill="currentColor" class="text-gray-100 dark:text-gray-800" opacity="0.3"/>
    
    <!-- Main Document -->
    <g transform="translate(140, 80)">
        <!-- Document Base -->
        <rect x="0" y="0" width="90" height="120" rx="4" fill="currentColor" class="text-gray-200 dark:text-gray-700"/>
        
        <!-- Document Lines -->
        <line x1="15" y1="25" x2="75" y2="25" stroke="currentColor" class="text-gray-300 dark:text-gray-600" stroke-width="3" stroke-linecap="round"/>
        <line x1="15" y1="40" x2="60" y2="40" stroke="currentColor" class="text-gray-300 dark:text-gray-600" stroke-width="3" stroke-linecap="round"/>
        <line x1="15" y1="55" x2="75" y2="55" stroke="currentColor" class="text-gray-300 dark:text-gray-600" stroke-width="3" stroke-linecap="round"/>
        <line x1="15" y1="70" x2="50" y2="70" stroke="currentColor" class="text-gray-300 dark:text-gray-600" stroke-width="3" stroke-linecap="round"/>
    </g>
    
    <!-- Second Document (Behind) -->
    <g transform="translate(160, 70)">
        <rect x="0" y="0" width="90" height="120" rx="4" fill="currentColor" class="text-gray-300 dark:text-gray-600" opacity="0.5"/>
    </g>
    
    <!-- Third Document (Behind Second) -->
    <g transform="translate(150, 75)">
        <rect x="0" y="0" width="90" height="120" rx="4" fill="currentColor" class="text-gray-300 dark:text-gray-600" opacity="0.3"/>
    </g>
    
    <!-- Magnifying Glass -->
    <g transform="translate(220, 150)">
        <!-- Handle -->
        <line x1="30" y1="30" x2="50" y2="50" stroke="currentColor" class="text-gray-400 dark:text-gray-500" stroke-width="6" stroke-linecap="round"/>
        
        <!-- Glass Circle -->
        <circle cx="15" cy="15" r="20" fill="none" stroke="currentColor" class="text-gray-400 dark:text-gray-500" stroke-width="6"/>
        <circle cx="15" cy="15" r="18" fill="white" opacity="0.2"/>
        
        <!-- Glass Shine -->
        <path d="M 8 10 Q 10 8, 12 10" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.6"/>
    </g>
    
    <!-- Decorative Elements -->
    <circle cx="100" cy="100" r="4" fill="currentColor" class="text-gray-300 dark:text-gray-600" opacity="0.4"/>
    <circle cx="300" cy="120" r="3" fill="currentColor" class="text-gray-300 dark:text-gray-600" opacity="0.4"/>
    <circle cx="110" cy="200" r="3" fill="currentColor" class="text-gray-300 dark:text-gray-600" opacity="0.4"/>
    <circle cx="290" cy="200" r="4" fill="currentColor" class="text-gray-300 dark:text-gray-600" opacity="0.4"/>
</svg>
