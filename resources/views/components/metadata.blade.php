@props(['title', 'crawler' => ''])

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="A social media website that allows you to tune in and share your opinion with people around the world.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="social-media, tune-in-media">
        <meta name="author" content="MarkTuning">
        <meta name="mobile-web-app-capable" content="no">
        <meta name="theme-color" content="#4079c7">
        <meta name="robots" content="{{ $crawler === '' ? 'noindex' : $crawler }}">
        <meta name="googlebot" content="{{ $crawler === '' ? 'noindex' : $crawler }}">
        <link rel="icon" sizes="192x192" href="{{ asset('/images/TuneInMediaLogoSingleColor.svg') }}">

        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/profile.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/post.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/welcome.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/sidebar.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/login.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/register.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/tailwind.min.css') }}">
        <script type="text/javascript" src="{{ asset('/js/app.js') }}"></script>
        
        <title>{{ $title }}</title>
    </head>
    
    <body>
        {{ $slot }}
    </body>
</html>
