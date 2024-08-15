@props(['title'])

<div class="py-12" {{ $attributes }}>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @isset($title)
            <header class="pb-8">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">{{ $title }}</h1>
            </header>
        @endisset

        {{ $slot }}

    </div>
</div>
