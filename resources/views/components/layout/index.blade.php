<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>
<body>
<x-nav></x-nav>
{{ $slot }}

<x-htmx-error-handler></x-htmx-error-handler>

</body>
</html>
