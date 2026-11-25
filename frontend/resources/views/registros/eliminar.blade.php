<form class="d-inline" method="post" action="{{ route('registros.destroy', [$modulo, $record['id']]) }}" onsubmit="return confirm('¿Eliminar este registro?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
</form>
