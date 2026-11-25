<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ClienteController extends RegistroController
{
    protected string $model = Cliente::class;
    protected array $detailRelations = ['coches'];

    protected function rules(?Model $record): array
    {
        $cedula = Rule::unique('clientes', 'cedula');

        return [
            'cedula' => ['required', 'string', 'max:20', $record ? $cedula->ignore($record) : $cedula],
            'direccion' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:20'],
        ];
    }

    public function coches(string $id)
    {
        return response()->json(Cliente::findOrFail($id)->coches()->orderBy('id')->get());
    }
}
