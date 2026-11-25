@if($revisiones)
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Código</th><th scope="col">Aceite</th><th scope="col">Filtro</th><th scope="col">Frenos</th></tr>
            </thead>
            <tbody>
                @foreach($revisiones as $revision)
                    <tr>
                        <td><a href="{{ route('registros.show', ['revisiones', $revision['id']]) }}">{{ $revision['codigo'] }}</a></td>
                        @foreach(['aceite', 'filtro', 'frenos'] as $component)
                            <td>@include('registros.valor', ['record' => $revision, 'name' => $component])</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-body-secondary mb-0">Aún no hay revisiones registradas.</p>
@endif
