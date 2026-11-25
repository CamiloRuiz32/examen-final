<?php

return [
    'api' => env('CONCESIONARIO_API_URL', 'http://127.0.0.1:8000/api'),
    'modulos' => [
        'clientes' => [
            'title' => 'Clientes',
            'singular' => 'cliente',
            'fields' => [
                'cedula' => ['Cédula', 'text', 20],
                'direccion' => ['Dirección', 'text', 150],
                'telefono' => ['Teléfono', 'tel', 20],
            ],
        ],
        'coches' => [
            'title' => 'Coches',
            'singular' => 'coche',
            'fields' => [
                'marca' => ['Marca', 'text', 80],
                'modelo' => ['Modelo', 'text', 80],
                'color' => ['Color', 'text', 40],
                'precio' => ['Precio', 'number'],
                'cliente_id' => ['Cliente propietario', 'select'],
                'foto' => ['Fotografía', 'file'],
            ],
        ],
        'revisiones' => [
            'title' => 'Revisiones técnicas',
            'singular' => 'revisión',
            'fields' => [
                'codigo' => ['Código', 'text', 20],
                'coche_id' => ['Coche', 'select'],
                'aceite' => ['Aceite', 'checkbox'],
                'filtro' => ['Filtro', 'checkbox'],
                'frenos' => ['Frenos', 'checkbox'],
            ],
        ],
    ],
];
