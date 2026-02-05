@props(['href' => '#', 'logo' => null, 'name'])

<a href="{{ $href }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
    @if(isset($logo))
        <img src="{{ $logo }}" alt="{{ $name }}" class="size-6 rounded shrink-0" />
    @elseif($slot->isEmpty())
        <div class="size-6 rounded shrink-0 bg-accent text-accent-foreground flex items-center justify-center">
            <i class="font-serif font-bold">{{ substr($name, 0, 1) }}</i>
        </div>
    @else
        {{ $slot }}
    @endif
    
    <span class="font-semibold text-sm">{{ $name }}</span>
</a>
