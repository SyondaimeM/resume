<!DOCTYPE html>
<html lang="en" light-mode="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $portfolio['profile']['name'] }} | @yield('title')</title>
    <meta name="description" content="@yield('description')">
    <meta property="og:site_name" content="{{ $portfolio['profile']['name'] }}">
    <meta property="og:title" content="{{ $portfolio['profile']['name'] }} | @yield('title')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/new/style.css') }}">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/new/cursor.css') }}">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/new/portfolio.css') }}">
    <script src="{{ \App\Support\VersionedAsset::url('js/new/error-reporting.js') }}"></script>
    @yield('styles')
</head>
<body>
    <main class="portfolio-page">
        @yield('content')
    </main>
    <div class="cursor-dot-outline" aria-hidden="true"></div>
    <div class="cursor-dot" aria-hidden="true"></div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    @include('partials.portfolio-site-data')
    <script src="{{ \App\Support\VersionedAsset::url('js/new/app.js') }}"></script>
    <script src="{{ \App\Support\VersionedAsset::url('js/new/cursor.js') }}"></script>
</body>
</html>
