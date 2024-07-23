@props(['title'])

<div {{ $attributes->merge(['class' => 'py-12']) }}>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        @isset($title)
            <header class="pb-8">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">{{ $title }}</h1>
            </header>
        @endisset

        {{ $slot }}

    </div>
</div>
