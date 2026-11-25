@extends('layout')
@section('title', 'Coches y revisiones')

@section('content')
<h1 class="h2 mb-4">Coches y revisiones</h1>
@forelse($coches as $coche)
    <section class="mb-3">
        <div class="border rounded p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                @if($coche['foto_url'])
                    <img class="photo rounded" src="{{ $coche['foto_url'] }}" alt="">
                @endif
                <div>
                    <h2 class="h5 mb-1"><a href="{{ route('registros.show', ['coches', $coche['id']]) }}">{{ $coche['marca'] }} {{ $coche['modelo'] }}</a></h2>
                    <p class="mb-0 text-body-secondary">{{ $coche['color'] }} · Propietario {{ $coche['cliente']['cedula'] }}</p>
                </div>
            </div>
            @include('registros.historial', ['revisiones' => $coche['revisiones']])
        </div>
    </section>
@empty
    <p class="text-body-secondary">Aún no hay coches registrados.</p>
@endforelse
@endsection
