<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadesSeeder extends Seeder
{
    public function run()
    {

        // Insertar los órganos de alta dirección
        DB::table('entidades')->insert([
            ['id' => 1, 'nombre' => 'Asamblea Universitaria', 'siglas' => 'AU', 'tipo' => 'Órgano de Alta Dirección', 'entidad_superior_id' => null, 'eliminado' => false],
            ['id' => 2, 'nombre' => 'Consejo Universitario', 'siglas' => 'CU', 'tipo' => 'Órgano de Alta Dirección', 'entidad_superior_id' => 1, 'eliminado' => false],
            ['id' => 3, 'nombre' => 'Rectorado', 'siglas' => 'RECT', 'tipo' => 'Órgano de Alta Dirección', 'entidad_superior_id' => 2, 'eliminado' => false],
            ['id' => 4, 'nombre' => 'Vicerrectorado Académico', 'siglas' => 'VRA', 'tipo' => 'Órgano de Alta Dirección', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['id' => 5, 'nombre' => 'Vicerrectorado de Investigación', 'siglas' => 'VRI', 'tipo' => 'Órgano de Alta Dirección', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['id' => 6, 'nombre' => 'Escuela de Posgrado', 'siglas' => 'EP', 'tipo' => 'Unidad', 'entidad_superior_id' => 3, 'eliminado' => false],
        ]);

        // Insertar órganos especiales
        DB::table('entidades')->insert([
            ['nombre' => 'Defensoría Universitaria', 'siglas' => 'DU', 'tipo' => 'Órgano Especial', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Tribunal de Honor Universitario', 'siglas' => 'THU', 'tipo' => 'Órgano Especial', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Comisión Permanente de Fiscalización', 'siglas' => 'CPF', 'tipo' => 'Órgano Especial', 'entidad_superior_id' => null, 'eliminado' => false],
        ]);

        // Insertar órganos de asesoramiento
        DB::table('entidades')->insert([
            ['nombre' => 'Oficina de Asesoría Jurídica', 'siglas' => 'OAJ', 'tipo' => 'Órgano de Asesoramiento', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Planeamiento y Presupuesto', 'siglas' => 'OPP', 'tipo' => 'Órgano de Asesoramiento', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Gestión de la Calidad', 'siglas' => 'OGC', 'tipo' => 'Órgano de Asesoramiento', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Cooperación y Relaciones Internacionales', 'siglas' => 'OCRI', 'tipo' => 'Órgano de Asesoramiento', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Comunicación e Imagen Institucional', 'siglas' => 'OCII', 'tipo' => 'Órgano de Asesoramiento', 'entidad_superior_id' => 3, 'eliminado' => false],
        ]);

        // Insertar órganos de apoyo
        DB::table('entidades')->insert([
            ['nombre' => 'Dirección General de Administración', 'siglas' => 'DGA', 'tipo' => 'Órgano de Apoyo', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Tecnologías de la Información', 'siglas' => 'OTI', 'tipo' => 'Órgano de Apoyo', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Secretaría General', 'siglas' => 'SG', 'tipo' => 'Órgano de Apoyo', 'entidad_superior_id' => 3, 'eliminado' => false],
        ]);

        // Insertar órganos del Vicerrectorado Académico
        DB::table('entidades')->insert([
            ['nombre' => 'Dirección de Responsabilidad Social Universitaria', 'siglas' => 'DRSU', 'tipo' => 'Unidad', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Dirección de Bienestar Universitario', 'siglas' => 'DBU', 'tipo' => 'Unidad', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Dirección de Servicios Académicos', 'siglas' => 'DSA', 'tipo' => 'Unidad', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Unidad del Sistema de Bibliotecas', 'siglas' => 'USB', 'tipo' => 'Unidad', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Dirección de Admisión', 'siglas' => 'DA', 'tipo' => 'Unidad', 'entidad_superior_id' => 4, 'eliminado' => false],
        ]);

        // Insertar órganos del Vicerrectorado de Investigación
        DB::table('entidades')->insert([
            ['nombre' => 'Dirección de Producción de Bienes y Servicios', 'siglas' => 'DPBS', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Dirección de Incubadora de Empresas', 'siglas' => 'DIE', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Dirección de Innovación y Transferencia Tecnológica', 'siglas' => 'DITT', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Instituto de Investigación', 'siglas' => 'II', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Unidad de Editorial Universitaria', 'siglas' => 'UEU', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
        ]);


        // Insertar facultades y sus unidades
        $facultades = [
            ['nombre' => 'Facultad de Agronomía', 'siglas' => 'FA'],
            ['nombre' => 'Facultad de Ciencias Biológicas', 'siglas' => 'FCB'],
            ['nombre' => 'Facultad de Ciencias Económicas, Administrativas y Contables', 'siglas' => 'FCEAC'],
            ['nombre' => 'Facultad de Ciencias Físicas y Matemáticas', 'siglas' => 'FCFM'],
            ['nombre' => 'Facultad de Derecho y Ciencias Políticas', 'siglas' => 'FDCP'],
            ['nombre' => 'Facultad de Enfermería', 'siglas' => 'FE'],
            ['nombre' => 'Facultad de Ingeniería Agrícola', 'siglas' => 'FIA'],
            ['nombre' => 'Facultad de Ingeniería Civil, Sistemas y Arquitectura', 'siglas' => 'FICSA'],
            ['nombre' => 'Facultad de Ingeniería Mecánica y Eléctrica', 'siglas' => 'FIME'],
            ['nombre' => 'Facultad de Ingeniería Química e Industrias Alimentarias', 'siglas' => 'FIQIA'],
            ['nombre' => 'Facultad de Ciencias Histórico Sociales y Educación', 'siglas' => 'FCHSE'],
            ['nombre' => 'Facultad de Medicina Humana', 'siglas' => 'FMH'],
            ['nombre' => 'Facultad de Medicina Veterinaria', 'siglas' => 'FMV'],
            ['nombre' => 'Facultad de Ingeniería Zootecnia', 'siglas' => 'FIZ'],
        ];

        foreach ($facultades as $facultad) {
            $idFacultad = DB::table('entidades')->insertGetId([
                'nombre' => $facultad['nombre'],
                'siglas' => $facultad['siglas'],
                'tipo' => 'Facultad',
                'entidad_superior_id' => null,
                'eliminado' => false,
            ]);

            $unidades = ['Consejo de Facultad', 'Decanato', 'Departamento Académico', 'Unidad de Investigación', 'Unidad de Posgrado'];
            foreach ($unidades as $unidad) {
                DB::table('entidades')->insert([
                    'nombre' => $unidad,
                    'siglas' => substr($unidad, 0, 3),
                    'tipo' => 'Unidad',
                    'entidad_superior_id' => $idFacultad,
                    'eliminado' => false,
                ]);
            }
        }

        $entidades = [
            ['nombre' => 'Decanatos', 'siglas' => 'DEC', 'tipo' => 'Unidad', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Direcciones de Escuelas', 'siglas' => 'DIREC', 'tipo' => 'Unidad', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Departamentos Académicos', 'siglas' => 'DEPA', 'tipo' => 'Unidad', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Decanatos, Direcciones de Escuelas', 'siglas' => 'DEC-DIREC', 'tipo' => 'Combinado', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Decanatos, Departamentos Académicos', 'siglas' => 'DEC-DEPA', 'tipo' => 'Combinado', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Direcciones de Escuelas, Departamentos Académicos', 'siglas' => 'DIREC-DEPA', 'tipo' => 'Combinado', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Decanatos, Direcciones de Escuelas, Departamentos Académicos', 'siglas' => 'DEC-DIREC-DEPA', 'tipo' => 'Combinado', 'entidad_superior_id' => null, 'eliminado' => 0],
        ];

        DB::table('entidades')->insert($entidades);



    }
}