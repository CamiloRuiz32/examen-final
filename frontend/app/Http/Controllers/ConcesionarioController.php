<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConcesionarioController extends Controller
{
    public function index(string $modulo)
    {
        return view('registros.index', [
            ...$this->module($modulo),
            'rows' => $this->read($modulo),
        ]);
    }

    public function create(string $modulo)
    {
        return $this->form($modulo, []);
    }

    public function store(Request $request, string $modulo)
    {
        return $this->save($request, $modulo);
    }

    public function show(string $modulo, string $id)
    {
        $record = $this->read("$modulo/$id");

        if ($modulo === 'clientes') {
            $record['coches'] = $this->read("clientes/$id/coches");
        }

        if ($modulo === 'coches') {
            $record['revisiones'] = $this->read("coches/$id/revisiones");
        }

        return view('registros.show', [...$this->module($modulo), 'record' => $record]);
    }

    public function edit(string $modulo, string $id)
    {
        return $this->form($modulo, $this->read("$modulo/$id"));
    }

    public function update(Request $request, string $modulo, string $id)
    {
        return $this->save($request, $modulo, $id);
    }

    public function destroy(string $modulo, string $id)
    {
        $response = $this->send(fn () => $this->api()->delete("$modulo/$id"));

        if ($response?->successful()) {
            return redirect()->route('registros.index', $modulo)->with('success', $response->json('message'));
        }

        return back()->withErrors(['api' => $this->error($response)]);
    }

    public function destroyFoto(string $id)
    {
        $response = $this->send(fn () => $this->api()->delete("coches/$id/foto"));

        return $response?->successful()
            ? back()->with('success', $response->json('message'))
            : back()->withErrors(['api' => $this->error($response)]);
    }

    public function consulta()
    {
        return view('consulta', ['coches' => $this->read('coches/con-revisiones')]);
    }

    private function form(string $modulo, array $record)
    {
        $options = match ($modulo) {
            'coches' => $this->read('clientes'),
            'revisiones' => $this->read('coches'),
            default => [],
        };

        return view('registros.form', [...$this->module($modulo), 'record' => $record, 'options' => $options]);
    }

    private function save(Request $request, string $modulo, ?string $id = null)
    {
        $fields = $this->module($modulo)['module']['fields'];
        $rules = [];

        foreach ($fields as $name => $field) {
            $type = $field[1];
            $rules[$name] = match ($type) {
                'number' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
                'select' => ['required', 'integer'],
                'checkbox' => ['required', 'boolean'],
                'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                default => ['required', 'string', 'max:'.$field[2]],
            };
        }

        $data = $request->validate($rules, attributes: array_map(fn ($field) => $field[0], $fields));
        $photo = $data['foto'] ?? null;
        unset($data['foto']);

        $url = $id ? "$modulo/$id" : $modulo;

        if ($id === null) {
            $url = match ($modulo) {
                'coches' => "clientes/{$data['cliente_id']}/coches",
                'revisiones' => "coches/{$data['coche_id']}/revisiones",
                default => $modulo,
            };
        }

        $response = $this->send(function () use ($photo, $data, $url, $id) {
            if ($photo) {
                if ($id) {
                    $data['_method'] = 'PUT';
                }

                $stream = fopen($photo->getRealPath(), 'rb');

                try {
                    return $this->api()->attach('foto', $stream, $photo->hashName())->post($url, $data);
                } finally {
                    fclose($stream);
                }
            }

            return $id ? $this->api()->put($url, $data) : $this->api()->post($url, $data);
        });

        if ($response?->successful()) {
            $message = $id ? 'Cambios guardados.' : 'Registro creado.';
            if ($photo && $response->json('foto_pendiente')) {
                $message .= ' Fotografía guardada. Pendiente de sincronizar con Cloudinary.';
            }

            return redirect()->route('registros.show', [$modulo, $response->json('id')])
                ->with('success', $message);
        }

        return back()->withInput()->withErrors($response?->json('errors') ?? ['api' => $this->error($response)]);
    }

    private function module(string $modulo): array
    {
        return ['modulo' => $modulo, 'module' => config("concesionario.modulos.$modulo")];
    }

    private function api()
    {
        return Http::baseUrl(config('concesionario.api'))->acceptJson()->connectTimeout(3)->timeout(35);
    }

    private function send(callable $call): ?Response
    {
        try {
            return $call();
        } catch (ConnectionException $exception) {
            report($exception);

            return null;
        }
    }

    private function read(string $url): array
    {
        $response = $this->send(fn () => $this->api()->get($url));

        if (! $response?->successful()) {
            abort($response?->status() === 404 ? 404 : 503, $this->error($response));
        }

        return $response->json();
    }

    private function error(?Response $response): string
    {
        if ($response === null) {
            return 'No se pudo conectar con la API del concesionario.';
        }

        return $response->serverError() && ! in_array($response->status(), [502, 503], true)
            ? 'La API del concesionario no está disponible en este momento.'
            : ($response->json('message') ?: 'La API rechazó la operación.');
    }
}
