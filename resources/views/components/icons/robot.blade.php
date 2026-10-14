<svg 
    {{ $attributes->merge(['class' => 'h-5 w-5']) }}
    width="20"
    height="20"
    viewBox="0 0 24 24" 
    fill="none" 
    stroke="currentColor" 
    stroke-width="1.5"
    stroke-linecap="round" 
    stroke-linejoin="round"
    aria-hidden="true"
>
    <!-- Head -->
    <rect x="4" y="3" width="16" height="12" rx="3" />
    <!-- Antenna -->
    <line x1="12" y1="3" x2="12" y2="0" />
    <circle cx="12" cy="0" r="1.5" />
    <!-- Eyes -->
    <circle cx="8.5" cy="8" r="1.25" class="robot-eye" />
    <circle cx="15.5" cy="8" r="1.25" class="robot-eye" />
    <!-- Mouth -->
    <line x1="9" y1="12" x2="15" y2="12" />
    <!-- Body -->
    <rect x="6" y="16" width="12" height="8" rx="1.5" />
    <!-- Arms -->
    <line x1="4" y1="18" x2="6" y2="18" />
    <line x1="18" y1="18" x2="20" y2="18" />
    <!-- Legs -->
    <line x1="9" y1="24" x2="9" y2="27" />
    <line x1="15" y1="24" x2="15" y2="27" />
    <line x1="7" y1="27" x2="11" y2="27" />
    <line x1="13" y1="27" x2="17" y2="27" />
</svg>

<style>
    .robot-eye {
        animation: robot-blink 4s ease-in-out infinite;
        transform-origin: center;
        transform-box: fill-box;
    }
    
    @keyframes robot-blink {
        0%, 45%, 55%, 100% { 
            transform: scaleY(1); 
        }
        50% { 
            transform: scaleY(0.1); 
        }
    }
    
    @media (prefers-reduced-motion: reduce) {
        .robot-eye {
            animation: none;
        }
    }
</style>