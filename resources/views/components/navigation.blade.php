<div>
    @php
        $navigations = \App\Models\Navigation::active()->rootItems()->with('children')->get();
    @endphp

    <flux:sidebar.nav x-show="$store.sidebar && !$store.sidebar.collapsed" x-cloak>
        @foreach($navigations as $nav)
            @if($nav->children->isEmpty())
                {{-- Single item --}}
                @php
                    $href = $nav->route_name ? route($nav->route_name, $nav->route_params ?? []) : $nav->url;

                    // Check if this item is currently active
                    $isCurrent = false;
                    if ($nav->route_name && $nav->route_params) {
                        if (request()->routeIs($nav->route_name) &&
                            request()->route()->parameters() == $nav->route_params) {
                            $isCurrent = true;
                        }
                    } elseif ($nav->url) {
                        $urlPath = ltrim($nav->url, '/');
                        if (request()->is($urlPath, $urlPath . '/*')) {
                            $isCurrent = true;
                        }
                    }
                @endphp
                <flux:sidebar.item
                    icon="{{ $nav->icon }}"
                    href="{{ $href }}"
                    :current="$isCurrent"
                >{{ $nav->label }}</flux:sidebar.item>
            @else
                {{-- Group with children --}}
                @php
                    // Check if any child route is currently active for expansion
                    $isExpanded = false;
                    foreach ($nav->children as $child) {
                        if ($child->route_name && $child->route_params) {
                            // Check if current route matches this child
                            $childRouteName = $child->route_name;
                            $childRouteParams = $child->route_params;

                            // Check by route name and params
                            if (request()->routeIs($childRouteName) &&
                                request()->route()->parameters() == $childRouteParams) {
                                $isExpanded = true;
                                break;
                            }

                            // Also check by URL path as fallback
                            $childUrl = route($childRouteName, $childRouteParams);
                            $childPath = parse_url($childUrl, PHP_URL_PATH);
                            if (request()->is(ltrim($childPath, '/'), ltrim($childPath, '/') . '/*')) {
                                $isExpanded = true;
                                break;
                            }
                        }
                    }
                @endphp

                <flux:sidebar.group
                    expandable
                    :expanded="$isExpanded"
                    icon="{{ $nav->icon }}"
                    heading="{{ $nav->label }}"
                    class="grid"
                >
                    @foreach($nav->children as $child)
                        @php
                            $childHref = $child->route_name ? route($child->route_name, $child->route_params ?? []) : $child->url;

                            // Check if this child is currently active
                            $isChildActive = false;
                            if ($child->route_name && $child->route_params) {
                                // Check by route name and params
                                if (request()->routeIs($child->route_name) &&
                                    request()->route()->parameters() == $child->route_params) {
                                    $isChildActive = true;
                                } else {
                                    // Also check by URL path
                                    $childUrl = route($child->route_name, $child->route_params);
                                    $childPath = parse_url($childUrl, PHP_URL_PATH);
                                    if (request()->is(ltrim($childPath, '/'), ltrim($childPath, '/') . '/*')) {
                                        $isChildActive = true;
                                    }
                                }
                            }
                        @endphp
                        <flux:sidebar.item
                            wire:navigate
                            href="{{ $childHref }}"
                            :current="$isChildActive"
                        >{{ $child->label }}</flux:sidebar.item>
                    @endforeach
                </flux:sidebar.group>
            @endif
        @endforeach
    </flux:sidebar.nav>

    <!-- Collapsed Icons -->
    <div x-show="$store.sidebar && $store.sidebar.collapsed" x-cloak class="flex flex-col items-center py-2 -mt-5.5">
        @foreach($navigations as $nav)
            @if($nav->children->isEmpty())
                {{-- Single item icon --}}
                @php
                    $iconHref = $nav->route_name ? route($nav->route_name, $nav->route_params ?? []) : $nav->url;

                    // Check if this icon is currently active
                    $iconActive = false;
                    if ($nav->route_name && $nav->route_params) {
                        if (request()->routeIs($nav->route_name) &&
                            request()->route()->parameters() == $nav->route_params) {
                            $iconActive = true;
                        }
                    } elseif ($nav->url) {
                        $urlPath = ltrim($nav->url, '/');
                        if (request()->is($urlPath, $urlPath . '/*')) {
                            $iconActive = true;
                        }
                    }
                @endphp
                <a
                    href="{{ $iconHref }}"
                    title="{{ $nav->label }}"
                    class="p-2.5 my-1 rounded-full inline-flex items-center justify-center {{ $iconActive ? 'bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    <x-dynamic-component :component="'flux::icon.'.$nav->icon" class="size-4.5" />
                </a>
            @else
                {{-- Group with dropdown --}}
                @php
                    // Check if any child is active
                    $isActive = false;
                    foreach ($nav->children as $child) {
                        if ($child->route_name && $child->route_params) {
                            // Check if current route matches this child
                            $childRouteName = $child->route_name;
                            $childRouteParams = $child->route_params;

                            // Check by route name and params
                            if (request()->routeIs($childRouteName) &&
                                request()->route()->parameters() == $childRouteParams) {
                                $isActive = true;
                                break;
                            }

                            // Also check by URL path as fallback
                            $childUrl = route($childRouteName, $childRouteParams);
                            $childPath = parse_url($childUrl, PHP_URL_PATH);
                            if (request()->is(ltrim($childPath, '/'), ltrim($childPath, '/') . '/*')) {
                                $isActive = true;
                                break;
                            }
                        }
                    }
                @endphp

                <div class="my-1">
                    <flux:dropdown position="right" align="start">
                        <button
                            title="{{ $nav->label }}"
                            class="p-2.5 rounded-full inline-flex items-center justify-center {{ $isActive ? 'bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                        >
                            <x-dynamic-component :component="'flux::icon.'.$nav->icon" class="size-4.5" />
                        </button>
                        <flux:menu class="w-48">
                            @foreach($nav->children as $child)
                                @php
                                    $menuHref = $child->route_name ? route($child->route_name, $child->route_params ?? []) : $child->url;
                                @endphp
                                <flux:menu.item
                                    wire:navigate
                                    href="{{ $menuHref }}"
                                >{{ $child->label }}</flux:menu.item>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>
            @endif
        @endforeach
    </div>
</div>
