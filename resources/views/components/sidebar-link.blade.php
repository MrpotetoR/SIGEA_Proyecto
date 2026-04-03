@props(['href', 'active' => false])

@php
    $active = $active ?? request()->is(ltrim(parse_url($href, PHP_URL_PATH), '/') . '*');
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium bg-blue-700 text-white'
        : 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-200 hover:bg-[#04276B] hover:text-white transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
