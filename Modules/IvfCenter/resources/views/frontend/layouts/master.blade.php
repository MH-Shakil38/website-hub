@php
    $cssUrl = route('ivfcenter.asset', ['type' => 'css', 'file' => 'style.css']) . '?v=' . (@filemtime(module_path('IvfCenter', 'Public/assets/css/style.css')) ?: time());
    $jsUrl = route('ivfcenter.asset', ['type' => 'js', 'file' => 'main.js']) . '?v=' . (@filemtime(module_path('IvfCenter', 'Public/assets/js/main.js')) ?: time());
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IVF Center.com — Your Dream of Parenthood Is Our Priority')</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Standalone IVF Center Theme CSS -->
    <link rel="stylesheet" href="{{ $cssUrl }}">
    @stack('styles')
</head>
<body>

    @yield('content')

    <!-- Standalone IVF Center Theme JS -->
    <script src="{{ $jsUrl }}"></script>
    @stack('scripts')
</body>
</html>
