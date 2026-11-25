@extends('layout')
@php($editing = isset($record['id']))
@section('title', ($editing ? 'Editar ' : 'Registrar ').$module['singular'])

@section('content')
<a class="d-inline-block mb-3" href="{{ route('registros.index', $modulo) }}">Volver al listado</a>
<h1 class="h2 mb-4">@yield('title')</h1>
<div class="row">
    <div class="col-lg-8">
        <div class="border rounded p-3">
            @if(in_array($modulo, ['coches', 'revisiones']) && !$options)
                @php($requiredModule = $modulo === 'coches' ? 'clientes' : 'coches')
                <p class="alert alert-info">Para continuar, registra primero {{ $modulo === 'coches' ? 'un cliente' : 'un coche' }}.</p>
                <a href="{{ route('registros.create', $requiredModule) }}">Ir al formulario de registro</a>
            @else
                <form method="post" enctype="multipart/form-data" action="{{ $editing ? route('registros.update', [$modulo, $record['id']]) : route('registros.store', $modulo) }}">
                    @csrf
                    @if($editing)
                        @method('PUT')
                    @endif
                    <div class="row g-3">
                        @foreach($module['fields'] as $name => $field)
                            @php([$label, $type] = $field)
                            @php($value = old($name, $record[$name] ?? ''))
                            <div class="{{ $type === 'checkbox' ? 'col-sm-4' : 'col-12' }}">
                                @if($type === 'checkbox')
                                    <div class="form-check">
                                        <input type="hidden" name="{{ $name }}" value="0">
                                        <input class="form-check-input" id="{{ $name }}" name="{{ $name }}" type="checkbox" value="1" @checked($value)>
                                        <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                                    </div>
                                @else
                                    <label class="form-label fw-semibold" for="{{ $name }}">{{ $label }}</label>
                                    @if($type === 'select')
                                        <select class="form-select @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" required>
                                            <option value="">Seleccionar</option>
                                            @foreach($options as $option)
                                                <option value="{{ $option['id'] }}" @selected($value == $option['id'])>
                                                    {{ $option['cedula'] ?? ($option['marca'].' '.$option['modelo'].' · #'.$option['id']) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($type === 'file')
                                        @if($record['foto_url'] ?? false)
                                            <div class="mb-2"><img class="photo rounded" src="{{ $record['foto_url'] }}" alt="Fotografía actual"></div>
                                        @endif
                                        <input type="file" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" aria-describedby="foto-ayuda">
                                        <div id="foto-ayuda" class="form-text">Opcional. JPG, PNG o WEBP de hasta 2 MB.</div>
                                    @else
                                        <input class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" required
                                            @if(isset($field[2])) maxlength="{{ $field[2] }}" @endif
                                            @if($type === 'number') min="0.01" max="9999999999.99" step="0.01" @endif>
                                    @endif
                                @endif
                                @error($name)
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-primary mt-4">Guardar</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
