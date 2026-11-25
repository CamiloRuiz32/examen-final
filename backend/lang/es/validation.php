<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'max' => [
        'string' => 'El campo :attribute admite hasta :max caracteres.',
        'numeric' => 'El campo :attribute no puede superar :max.',
        'file' => 'El archivo :attribute no puede superar :max KB.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser mayor o igual que :min.',
    ],
    'numeric' => 'El campo :attribute debe ser numérico.',
    'decimal' => 'El campo :attribute admite hasta dos decimales.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'image' => 'El archivo :attribute debe ser una imagen.',
    'mimes' => 'El archivo :attribute debe ser JPG, PNG o WEBP.',
    'exists' => 'La opción seleccionada para :attribute no existe.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'uploaded' => 'No se pudo cargar :attribute.',
    'attributes' => [
        'cedula' => 'cédula',
        'direccion' => 'dirección',
        'telefono' => 'teléfono',
        'codigo' => 'código',
        'cliente_id' => 'cliente propietario',
        'coche_id' => 'coche',
        'foto' => 'fotografía',
    ],
];
