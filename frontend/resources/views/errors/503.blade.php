@extends('layout')
@section('title', 'Servicio no disponible')
@section('content')
<h1 class="h2">@yield('title')</h1>
<p>{{ $exception->getMessage() ?: 'Intenta nuevamente en unos momentos.' }}</p>
<a href="{{ route('inicio') }}">Volver al inicio</a>
@endsection
