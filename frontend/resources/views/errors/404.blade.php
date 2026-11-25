@extends('layout')
@section('title', 'Registro no encontrado')
@section('content')
<h1 class="h2">@yield('title')</h1>
<p>{{ $exception->getMessage() ?: 'No encontramos la página o el registro solicitado.' }}</p>
<a href="{{ route('inicio') }}">Volver al inicio</a>
@endsection
