<?php

namespace App\Http\Controllers;

use App\Models\Coche;
use App\Services\CloudinaryFotos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

abstract class RegistroController extends Controller
{
    protected string $model;
    protected array $relations = [];
    protected array $detailRelations = [];

    abstract protected function rules(?Model $record): array;

    public function index()
    {
        return response()->json($this->model::with($this->relations)->orderBy('id')->get());
    }

    public function show(string $id)
    {
        return response()->json($this->model::with($this->detailRelations)->findOrFail($id));
    }

    public function store(Request $request)
    {
        return response()->json($this->save($request), 201);
    }

    public function update(Request $request, string $id)
    {
        return response()->json($this->save($request, $id));
    }

    public function destroy(string $id)
    {
        try {
            $photo = DB::transaction(function () use ($id) {
                $record = $this->model::lockForUpdate()->findOrFail($id);
                $record->delete();

                return $record->foto;
            });
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451) {
                abort(400, 'El registro tiene datos asociados. Elimínalos o reasígnalos primero.');
            }

            throw $exception;
        }

        $this->deletePhoto($photo);

        return response()->json(['message' => 'Registro eliminado.']);
    }

    private function save(Request $request, ?string $id = null): Model
    {
        $newPhoto = null;

        try {
            [$record, $oldPhoto] = DB::transaction(function () use ($request, $id, &$newPhoto) {
                $record = $id === null ? new $this->model : $this->model::lockForUpdate()->findOrFail($id);
                $rules = $this->rules($record->exists ? $record : null);

                if ($request->isMethod('PATCH')) {
                    foreach ($rules as $field => $fieldRules) {
                        $rules[$field] = ['sometimes', ...$fieldRules];
                    }
                }

                $data = $request->validate($rules);
                $oldPhoto = null;

                if (isset($data['foto'])) {
                    $oldPhoto = $record->foto;
                    $newPhoto = $data['foto']->store(Coche::DIRECTORIO_PENDIENTES, 'local');
                    if ($newPhoto === false) {
                        throw new RuntimeException('No se pudo guardar la fotografía.');
                    }
                    $data['foto'] = $newPhoto;
                }

                $record->fill($data)->save();

                return [$record, $oldPhoto];
            });
        } catch (Throwable $exception) {
            $this->deletePhoto($newPhoto ?: null);

            if ($exception instanceof QueryException && ($exception->errorInfo[1] ?? null) === 1062) {
                $field = $request->has('cedula') ? 'cedula' : 'codigo';
                throw ValidationException::withMessages([$field => 'El valor ya está registrado.']);
            }

            if ($exception instanceof QueryException && ($exception->errorInfo[1] ?? null) === 1452) {
                $field = $request->has('cliente_id') ? 'cliente_id' : 'coche_id';
                throw ValidationException::withMessages([$field => 'El registro asociado ya no existe.']);
            }

            throw $exception;
        }

        $this->deletePhoto($oldPhoto);

        return $record->load($this->detailRelations);
    }

    protected function deletePhoto(?string $photo): void
    {
        if ($photo === null) {
            return;
        }

        try {
            if (str_starts_with($photo, 'https://res.cloudinary.com/')) {
                app(CloudinaryFotos::class)->eliminar($photo);
            } elseif (! Storage::disk('local')->delete($photo)) {
                throw new RuntimeException('No se pudo eliminar la fotografía '.$photo);
            }
        } catch (Throwable $exception) {
            report(new RuntimeException('No se pudo retirar la fotografía anterior.', previous: $exception));
        }
    }
}
