<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadesSeeder extends Seeder
{
    public function run()
    {
        $facultades = [
            ['nombre' => 'FACULTAD DE INGENIERÍA QUÍMICA E INDUSTRIAS ALIMENTARIAS', 'siglas' => 'FIQUIA'],
            ['nombre' => 'FACULTAD DE INGENIERÍA CIVIL, DE SISTEMAS Y DE ARQUITECTURA', 'siglas' => 'FICSA'],
            ['nombre' => 'FACULTAD DE CIENCIAS ECONÓMICAS ADMINISTRATIVAS Y CONTABLES', 'siglas' => 'FACEAC'],
            ['nombre' => 'FACULTAD DE ENFERMERÍA', 'siglas' => 'FE'],
            ['nombre' => 'FACULTAD DE INGENIERÍA MECÁNICA Y ELÉCTRICA', 'siglas' => 'FIME'],
            ['nombre' => 'FACULTAD DE DERECHO Y CIENCIA POLÍTICA', 'siglas' => 'FDCP'],
            ['nombre' => 'FACULTAD DE INGENIERÍA AGRÍCOLA', 'siglas' => 'FIA'],
            ['nombre' => 'FACULTAD DE CIENCIAS HISTÓRICO SOCIALES Y EDUCACIÓN', 'siglas' => 'FASCHE'],
            ['nombre' => 'FACULTAD DE CIENCIAS BIOLÓGICAS', 'siglas' => 'FCCBB'],
            ['nombre' => 'FACULTAD DE MEDICINA VETERINARIA', 'siglas' => 'FMV']
        ];

        $subentidades = ['Decanato', 'Departamento Académico', 'Mesa de Partes', 'Unidad de Investigación'];

        foreach ($facultades as $facultad) {
            $facultadId = DB::table('entidades')->insertGetId([
                'nombre' => $facultad['nombre'],
                'siglas' => $facultad['siglas'],
                'tipo' => 'Facultad',
                'entidad_superior_id' => null,
                'eliminado' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($subentidades as $subentidad) {
                DB::table('entidades')->insert([
                    'nombre' => "{$subentidad} - {$facultad['nombre']}",
                    'siglas' => substr($subentidad, 0, 3) . '-' . $facultad['siglas'],
                    'tipo' => 'Unidad',
                    'entidad_superior_id' => $facultadId,
                    'eliminado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}