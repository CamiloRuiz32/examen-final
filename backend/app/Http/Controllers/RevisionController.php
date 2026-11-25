<?php

namespace App\Http\Controllers;

use App\Models\{Coche, Revision};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RevisionController extends RegistroController
{
    protected string $model = Revision::class;
    protected array $relations = ['coche'];
    protected array $detailRelations = ['coche.cliente'];

    protected function rules(?Model $record): array
    {
        $codigo = Rule::unique('revisiones', 'codigo');

        return [
            'codigo' => ['required', 'string', 'max:20', $record ? $codigo->ignore($record) : $codigo],
            'aceite' => ['required', 'boolean'],
            'filtro' => ['required', 'boolean'],
            'frenos' => ['required', 'boolean'],
            'coche_id' => ['required', 'integer', 'exists:coches,id'],
        ];
    }

    public function storeForCoche(Request $request, string $id)
    {
        $request->merge(['coche_id' => Coche::findOrFail($id)->id]);

        return $this->store($request);
    }
}
