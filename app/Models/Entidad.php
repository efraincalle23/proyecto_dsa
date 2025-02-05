<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;

    protected $table = 'entidades';

    protected $fillable = [
        'nombre',
        'siglas',
        'tipo',
        'entidad_superior_id',
        'eliminado'
    ];

    // Relación con entidades hijas
    public function subEntidades()
    {
        return $this->hasMany(Entidad::class, 'entidad_superior_id');
    }

    // Relación con la entidad superior
    public function entidadSuperior()
    {
        return $this->belongsTo(Entidad::class, 'entidad_superior_id');
    }
}