<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inicio') · Concesionario</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        .photo { width: 110px; height: 74px; object-fit: cover; }
        .detail-photo { width: 100%; max-width: 560px; max-height: 360px; object-fit: contain; }
        td, dd { overflow-wrap: anywhere; }
    </style>
</head>
<body>
<header class="bg-body-tertiary border-bottom">
    <nav class="container py-2" aria-label="Navegación principal">
        <div class="nav nav-pills flex-wrap gap-1">
            <a class="nav-link @if(request()->routeIs('inicio')) active @endif" href="{{ route('inicio') }}">Inicio</a>
            @foreach(config('concesionario.modulos') as $key => $item)
                <a class="nav-link @if(request()->route('modulo') === $key) active @endif" href="{{ route('registros.index', $key) }}">{{ $item['title'] }}</a>
            @endforeach
            <a class="nav-link @if(request()->routeIs('consulta')) active @endif" href="{{ route('consulta') }}">Coches y revisiones</a>
        </div>
    </nav>
</header>
<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="fw-semibold mb-1">Revisa lo siguiente</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
