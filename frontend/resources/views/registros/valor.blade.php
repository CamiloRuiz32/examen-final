@switch($name)
    @case('foto')
        @if($record['foto_url'] ?? null)
            <img src="{{ $record['foto_url'] }}" class="rounded {{ $photoClass ?? 'photo' }}" alt="{{ $record['marca'] }} {{ $record['modelo'] }}">
        @else
            <span class="text-body-secondary">Sin fotografía</span>
        @endif
        @break
    @case('cliente_id')
        <a href="{{ route('registros.show', ['clientes', $record['cliente_id']]) }}">{{ $record['cliente']['cedula'] }}</a>
        @break
    @case('coche_id')
        <a href="{{ route('registros.show', ['coches', $record['coche_id']]) }}">{{ $record['coche']['marca'] }} {{ $record['coche']['modelo'] }}</a>
        @break
    @case('precio')
        {{ number_format((float) $record['precio'], 2, ',', '.') }}
        @break
    @case('aceite')
    @case('filtro')
    @case('frenos')
        <span class="badge {{ $record[$name] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $record[$name] ? 'Sí' : 'No' }}</span>
        @break
    @default
        {{ $record[$name] }}
@endswitch
