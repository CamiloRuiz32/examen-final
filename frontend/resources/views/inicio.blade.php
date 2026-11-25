@extends('layout')

@section('content')
<h1 class="h2 mb-3">Concesionario y taller</h1>
<ul class="list-group">
    @foreach(config('concesionario.modulos') as $key => $item)
        <li class="list-group-item">
            <a href="{{ route('registros.create', $key) }}">Registrar {{ $item['singular'] }}</a>
        </li>
    @endforeach
</ul>
@endsection
