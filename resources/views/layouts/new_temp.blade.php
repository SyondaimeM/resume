<!DOCTYPE html>
<html lang="en" light-mode="dark">

<head>
    <title>Sanish Resume</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--For Dark/Light mode Toggle-->
    <script src="https://code.iconify.design/1/1.0.4/iconify.min.js"></script>
    <!--Import Google Icon Font-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300&display=swap" rel="stylesheet">
    <script src='https://kit.fontawesome.com/a076d05399.js'></script>
    <link rel="stylesheet" href="{{asset('css/new/preloader.css')}}">
    <link rel="stylesheet" href="{{asset('css/new/home.css')}}">
    <link rel="stylesheet" href="{{asset('css/new/style.css')}}">
    <!-- Favicon -->
    <link id='favicon' rel="shortcut icon" href="{{asset('images/favicon.ico')}}" type="image/x-png">
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/jpswalsh/academicons@1/css/academicons.min.css">
    <script defer src="{{asset('js/new/dynamicTitle.js')}}"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    @yield('style')
    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-M11CMZ12Q"></script> -->
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-M11CMZ12Q');
    </script>
</head>

<body class="no-scroll-preload">
    <!-- loader -->
    <div class="loader-container">
        <div class="atom">
            <div class="electron"></div>
            <div class="electron-alpha"></div>
            <div class="electron-omega"></div>
        </div>
    </div>

    <br><br><br>
    <!-- Dynamic footer section -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    @yield('content')

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-G6D61F5TL4"></script>
    <!-- Fetching our Google Tag Manager -->

    <!--JavaScript at end of body for optimized loading-->
    @yield('scripts')
    <!-- <script src="assets/js/particle.js"></script> -->
</body>

</html>
