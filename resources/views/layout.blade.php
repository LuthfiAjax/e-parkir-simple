<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <main>
        @yield('content')
    </main>

    <!-- Tambahkan script JavaScript Bootstrap -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    @yield('javascript')
</body>
</html>
