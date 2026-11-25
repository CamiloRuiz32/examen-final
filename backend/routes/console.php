<?php

use App\Models\Coche;
use App\Services\CloudinaryFotos;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\HttpKernel\Exception\HttpException;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fotos:sincronizar', function (CloudinaryFotos $cloudinary) {
    $total = 0;

    foreach (Coche::where('foto', 'like', Coche::DIRECTORIO_PENDIENTES.'/%')->lazyById() as $coche) {
        try {
            if ($cloudinary->sincronizar($coche)) {
                $total++;
            }
        } catch (HttpException $error) {
            $this->warn($error->getMessage().' Las fotos pendientes se conservan en Laravel.');

            return 1;
        }
    }

    $this->info("Fotos sincronizadas: $total.");

    return 0;
})->purpose('Sube a Cloudinary las fotografías pendientes');

Schedule::command('fotos:sincronizar')->everyMinute()->withoutOverlapping(10);
