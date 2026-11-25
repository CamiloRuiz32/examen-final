<?php

namespace App\Services;

use App\Models\Coche;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryFotos
{
    public function sincronizar(Coche $pendiente): bool
    {
        $disco = Storage::disk('local');

        $guardada = DB::transaction(function () use ($pendiente, $disco) {
            $coche = Coche::lockForUpdate()->find($pendiente->id);

            if (! $coche || ! $coche->foto_pendiente || $coche->foto !== $pendiente->foto || ! $disco->exists($coche->foto)) {
                return false;
            }

            $archivo = new UploadedFile($disco->path($coche->foto), basename($coche->foto), test: true);
            $coche->update(['foto' => $this->subir($archivo)]);

            return true;
        });

        if ($guardada && ! $disco->delete($pendiente->foto)) {
            report(new RuntimeException('No se pudo retirar la copia local de la fotografía.'));
        }

        return $guardada;
    }

    public function subir(UploadedFile $foto): string
    {
        $publicId = 'concesionario/coches/'.Str::uuid();
        $parametros = $this->firmar(['overwrite' => 'false', 'public_id' => $publicId]);

        try {
            $respuesta = Http::acceptJson()->connectTimeout(3)->timeout(20)
                ->attach('file', $foto->get(), $foto->hashName())
                ->post($this->endpoint('upload'), $parametros);
        } catch (ConnectionException) {
            abort(502, 'No se pudo conectar con Cloudinary.');
        }

        $url = $respuesta->json('secure_url');
        abort_unless(
            $respuesta->successful() && is_string($url) && strlen($url) <= 255
                && $this->publicId($url) === $publicId,
            502,
            'Cloudinary no pudo guardar la fotografía.'
        );

        return $url;
    }

    public function eliminar(string $url): void
    {
        $publicId = $this->publicId($url);
        abort_unless($publicId, 502, 'No se pudo identificar la fotografía en Cloudinary.');
        $parametros = $this->firmar(['invalidate' => 'true', 'public_id' => $publicId]);

        $respuesta = Http::asForm()->acceptJson()->connectTimeout(3)->timeout(10)
            ->post($this->endpoint('destroy'), $parametros);

        abort_unless(
            $respuesta->successful() && in_array($respuesta->json('result'), ['ok', 'not found'], true),
            502,
            'Cloudinary no pudo eliminar la fotografía.'
        );
    }

    private function endpoint(string $accion): string
    {
        $cloud = config('services.cloudinary.cloud_name');

        return "https://api.cloudinary.com/v1_1/$cloud/image/$accion";
    }

    private function firmar(array $parametros): array
    {
        $cloud = config('services.cloudinary.cloud_name');
        $key = config('services.cloudinary.api_key');
        $secret = config('services.cloudinary.api_secret');
        abort_unless(
            is_string($cloud) && preg_match('/^[a-zA-Z0-9_-]+$/D', $cloud) && $key && $secret,
            503,
            'Falta configurar Cloudinary en el backend.'
        );

        $parametros['timestamp'] = (string) time();
        ksort($parametros);
        $partes = [];
        foreach ($parametros as $nombre => $valor) {
            $partes[] = "$nombre=$valor";
        }

        $parametros['signature'] = hash('sha256', implode('&', $partes).$secret);
        $parametros['api_key'] = $key;

        return $parametros;
    }

    private function publicId(string $url): ?string
    {
        $cloud = preg_quote((string) config('services.cloudinary.cloud_name'), '~');
        preg_match("~^https://res\.cloudinary\.com/$cloud/image/upload/v[0-9]+/(concesionario/coches/[a-f0-9-]{36})\.[a-z0-9]+$~D", $url, $matches);

        return $matches[1] ?? null;
    }
}
