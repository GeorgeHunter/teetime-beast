<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TicketBeast')</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    {{--@include('scripts.app')--}}
</head>
<body class="bg-dark">
<div id="app">
    @yield('content')
</div>

@stack('beforeScripts')
<script src="{{ mix('js/app.js') }}"></script>
@stack('afterScripts')
{{--{{ svg_spritesheet() }}--}}
</body>
</html>