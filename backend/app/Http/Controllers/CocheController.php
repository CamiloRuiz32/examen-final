<?php

namespace App\Http\Controllers;

use App\Models\{Cliente, Coche};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CocheController extends RegistroController
{
    protected string $model = Coche::class;
    protected array $relations = ['cliente'];
    protected array $detailRelations = ['cliente', 'revisiones'];

    protected function rules(?Model $record): array
    {
        return [
            'marca' => ['required', 'string', 'max:80'],
            'modelo' => ['required', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:40'],
            'precio' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'foto' => ['sometimes', 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function storeForCliente(Request $request, string $id)
    {
        $request->merge(['cliente_id' => Cliente::findOrFail($id)->id]);

        return $this->store($request);
    }

    public function conRevisiones()
    {
        return response()->json(Coche::with($this->detailRelations)->orderBy('id')->get());
    }

    public function revisiones(string $id)
    {
        return response()->json(Coche::findOrFail($id)->revisiones()->orderBy('id')->get());
    }

    public function destroyFoto(string $id)
    {
        $path = DB::transaction(function () use ($id) {
            $coche = Coche::lockForUpdate()->findOrFail($id);
            $path = $coche->foto;
            $coche->update(['foto' => null]);

            return $path;
        });

        $this->deletePhoto($path);

        return response()->json(['message' => 'Fotografía eliminada.']);
    }

    public function foto(string $id)
    {
        $path = Coche::findOrFail($id)->foto;
        if (str_starts_with($path ?? '', 'https://res.cloudinary.com/')) {
            return redirect()->away($path);
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        abort_unless($path && $disk->exists($path), 404, 'Fotografía no encontrada.');

        return $disk->response($path, headers: ['Cache-Control' => 'no-cache', 'X-Content-Type-Options' => 'nosniff']);
    }
}
