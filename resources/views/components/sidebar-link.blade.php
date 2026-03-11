@props(['href', 'active' => false])

@php
    $active = $active ?? request()->is(ltrim(parse_url($href, PHP_URL_PATH), '/') . '*');
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium bg-indigo-700 text-white'
        : 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-indigo-200 hover:bg-indigo-700 hover:text-white transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
