<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles

    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="antialiased min-h-screen">
<x-nav.index />
{{ $slot }}

@filamentScripts
@livewireScripts
@vite('resources/js/app.js')
</body>
</html>
