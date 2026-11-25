<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coche extends Model
{
    public const DIRECTORIO_PENDIENTES = 'coches/pendientes';

    protected $table = 'coches';
    public $timestamps = false;
    protected $fillable = ['marca', 'modelo', 'color', 'precio', 'foto', 'cliente_id'];

    protected $casts = ['precio' => 'decimal:2'];
    protected $appends = ['foto_url', 'foto_pendiente'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (str_starts_with($this->foto ?? '', 'https://res.cloudinary.com/')) {
            return $this->foto;
        }

        return $this->foto ? route('coches.foto', $this->id) : null;
    }

    public function getFotoPendienteAttribute(): bool
    {
        return str_starts_with($this->foto ?? '', self::DIRECTORIO_PENDIENTES.'/');
    }
}
