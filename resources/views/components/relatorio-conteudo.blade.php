@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'py-12']) }}>
    <div class="relatorio-page max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $class }}">
        {{ $slot }}
    </div>
</div>
