@extends('layout')

@section('title', match($modulo) {
    'clientes' => 'Cliente '.$record['cedula'],
    'coches' => $record['marca'].' '.$record['modelo'],
    'revisiones' => 'Revisión '.$record['codigo'],
})

@section('content')
<a href="{{ route('registros.index', $modulo) }}" class="d-inline-block mb-3">Volver al listado</a>
<div class="d-flex justify-content-between gap-3 flex-wrap align-items-center mb-4">
    <h1 class="h2 mb-0">@yield('title')</h1>
    <div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('registros.edit', [$modulo, $record['id']]) }}">Editar</a>
        @include('registros.eliminar')
    </div>
</div>
<div class="border rounded p-3 mb-4">
    <dl class="row mb-0">
        @foreach($module['fields'] as $name => [$label])
            <dt class="col-sm-4 col-lg-3">{{ $label }}</dt>
            <dd class="col-sm-8 col-lg-9">
                @include('registros.valor', ['photoClass' => 'detail-photo'])
                @if($name === 'foto' && ($record['foto_url'] ?? false))
                    <form method="post" class="mt-2" action="{{ route('coches.foto.destroy', $record['id']) }}" onsubmit="return confirm('¿Eliminar la fotografía?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Eliminar fotografía</button>
                    </form>
                @endif
            </dd>
        @endforeach
    </dl>
</div>

@if($modulo === 'clientes')
    <h2 class="h4">Coches del cliente</h2>
    <div class="list-group">
        @forelse($record['coches'] as $coche)
            <a class="list-group-item list-group-item-action d-flex align-items-center gap-3" href="{{ route('registros.show', ['coches', $coche['id']]) }}">
                @if($coche['foto_url'])
                    <img class="photo rounded" src="{{ $coche['foto_url'] }}" alt="">
                @endif
                {{ $coche['marca'] }} {{ $coche['modelo'] }} · {{ $coche['color'] }}
            </a>
        @empty
            <p class="text-body-secondary">Este cliente aún no tiene coches registrados.</p>
        @endforelse
    </div>
@endif

@if($modulo === 'coches')
    <h2 class="h4">Historial de revisiones</h2>
    @include('registros.historial', ['revisiones' => $record['revisiones']])
@endif
@endsection
