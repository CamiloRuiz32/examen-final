@extends('layout')
@section('title', $module['title'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h2 mb-0">{{ $module['title'] }}</h1>
    <a class="btn btn-primary" href="{{ route('registros.create', $modulo) }}">Registrar {{ $module['singular'] }}</a>
</div>
@if($rows)
    <div class="border rounded">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($module['fields'] as [$label])
                            <th scope="col">{{ $label }}</th>
                        @endforeach
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $record)
                        <tr>
                            @foreach($module['fields'] as $name => $field)
                                <td>@include('registros.valor')</td>
                            @endforeach
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('registros.show', [$modulo, $record['id']]) }}">Ver</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('registros.edit', [$modulo, $record['id']]) }}">Editar</a>
                                @include('registros.eliminar')
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p class="text-body-secondary">Aún no hay registros.</p>
@endif
@endsection
