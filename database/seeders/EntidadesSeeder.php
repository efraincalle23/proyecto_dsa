<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadesSeeder extends Seeder
{
    public function run()
    {
        // 1. Insertar órganos de alta dirección
        DB::table('entidades')->insert([
            ['id' => 1, 'nombre' => 'Asamblea Universitaria', 'siglas' => 'AU', 'tipo' => 'Oficina', 'entidad_superior_id' => null, 'eliminado' => false],
            ['id' => 2, 'nombre' => 'Consejo Universitario', 'siglas' => 'CU', 'tipo' => 'Oficina', 'entidad_superior_id' => 1, 'eliminado' => false],
            ['id' => 3, 'nombre' => 'Rectorado', 'siglas' => 'RECT', 'tipo' => 'Oficina', 'entidad_superior_id' => 2, 'eliminado' => false],
            ['id' => 4, 'nombre' => 'Vicerrectorado Académico', 'siglas' => 'VRA', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['id' => 5, 'nombre' => 'Vicerrectorado de Investigación', 'siglas' => 'VRI', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['id' => 6, 'nombre' => 'Escuela de Posgrado', 'siglas' => 'EP', 'tipo' => 'Escuela', 'entidad_superior_id' => 3, 'eliminado' => false],
        ]);

        // 2. Insertar órganos especiales
        DB::table('entidades')->insert([
            ['nombre' => 'Defensoría Universitaria', 'siglas' => 'DU', 'tipo' => 'Oficina', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Tribunal de Honor Universitario', 'siglas' => 'THU', 'tipo' => 'Oficina', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Comisión Permanente de Fiscalización', 'siglas' => 'CPF', 'tipo' => 'Oficina', 'entidad_superior_id' => null, 'eliminado' => false],
        ]);

        // 3. Insertar órganos de asesoramiento
        DB::table('entidades')->insert([
            ['nombre' => 'Oficina de Asesoría Jurídica', 'siglas' => 'OAJ', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Planeamiento y Presupuesto', 'siglas' => 'OPP', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Gestión de la Calidad', 'siglas' => 'OGC', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Cooperación y Relaciones Internacionales', 'siglas' => 'OCRI', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Comunicación e Imagen Institucional', 'siglas' => 'OCII', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
        ]);

        // 4. Insertar órganos de apoyo
        DB::table('entidades')->insert([
            ['nombre' => 'Dirección General de Administración', 'siglas' => 'DGA', 'tipo' => 'Dirección', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Oficina de Tecnologías de la Información', 'siglas' => 'OTI', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Secretaría General', 'siglas' => 'SG', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
        ]);

        // 5. Insertar órganos del Vicerrectorado Académico
        DB::table('entidades')->insert([
            ['nombre' => 'Dirección de Responsabilidad Social Universitaria', 'siglas' => 'DRSU', 'tipo' => 'Dirección', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Dirección de Bienestar Universitario', 'siglas' => 'DBU', 'tipo' => 'Dirección', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Dirección de Servicios Académicos', 'siglas' => 'DSA', 'tipo' => 'Dirección', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Unidad del Sistema de Bibliotecas', 'siglas' => 'USB', 'tipo' => 'Unidad', 'entidad_superior_id' => 4, 'eliminado' => false],
            ['nombre' => 'Dirección de Admisión', 'siglas' => 'DA', 'tipo' => 'Dirección', 'entidad_superior_id' => 4, 'eliminado' => false],
        ]);

        // 6. Insertar órganos del Vicerrectorado de Investigación
        DB::table('entidades')->insert([
            ['nombre' => 'Dirección de Producción de Bienes y Servicios', 'siglas' => 'DPBS', 'tipo' => 'Dirección', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Dirección de Incubadora de Empresas', 'siglas' => 'DIE', 'tipo' => 'Dirección', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Dirección de Innovación y Transferencia Tecnológica', 'siglas' => 'DITT', 'tipo' => 'Dirección', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Instituto de Investigación', 'siglas' => 'II', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
            ['nombre' => 'Unidad de Editorial Universitaria', 'siglas' => 'UEU', 'tipo' => 'Unidad', 'entidad_superior_id' => 5, 'eliminado' => false],
        ]);

        // 7. Insertar las nuevas oficinas bajo la Oficina General de Recursos Humanos
        DB::table('entidades')->insert([
            ['nombre' => 'Oficina General de Recursos Humanos', 'siglas' => 'OGRH', 'tipo' => 'Oficina', 'entidad_superior_id' => 3, 'eliminado' => false],
            ['nombre' => 'Of. General de Infraestructura y Servicios', 'siglas' => 'OGIS', 'tipo' => 'Oficina', 'entidad_superior_id' => 7, 'eliminado' => false],
            ['nombre' => 'Oficina de Control de Personal', 'siglas' => 'OCP', 'tipo' => 'Oficina', 'entidad_superior_id' => 7, 'eliminado' => false],
            ['nombre' => 'Oficina de Remuneraciones', 'siglas' => 'OR', 'tipo' => 'Oficina', 'entidad_superior_id' => 7, 'eliminado' => false],
            ['nombre' => 'Oficina de Escalafón y Evaluación', 'siglas' => 'OEE', 'tipo' => 'Oficina', 'entidad_superior_id' => 7, 'eliminado' => false],
        ]);
        // 7. Insertar Facultades (agregamos los campos 'tipo', 'entidad_superior_id' y 'eliminado')
        $facultades = [
            ['nombre' => 'Facultad de Ciencias Biológicas', 'siglas' => 'FCB'],
            ['nombre' => 'Facultad de Enfermería', 'siglas' => 'FE'],
            ['nombre' => 'Facultad de Medicina Humana', 'siglas' => 'FMH'],
            ['nombre' => 'Facultad de Medicina Veterinaria', 'siglas' => 'FMV'],
            ['nombre' => 'Facultad de Ingeniería Civil, de Sistemas y de Arquitectura', 'siglas' => 'FICSA'],
            ['nombre' => 'Facultad de Ciencias Físicas y Matemáticas', 'siglas' => 'FACFYM'],
            ['nombre' => 'Facultad de Ingeniería Mecánica y Eléctrica', 'siglas' => 'FIME'],
            ['nombre' => 'Facultad de Ingeniería Química e Industrias Alimentarias', 'siglas' => 'FIQIA'],
            ['nombre' => 'Facultad de Agronomía', 'siglas' => 'FA'],
            ['nombre' => 'Facultad de Ingeniería Agrícola', 'siglas' => 'FIA'],
            ['nombre' => 'Facultad de Ingeniería Zootecnia', 'siglas' => 'FIZ'],
            ['nombre' => 'Facultad de Ciencias Histórico Sociales y Educación', 'siglas' => 'FACHSE'],
            ['nombre' => 'Facultad de Derecho y Ciencia Política', 'siglas' => 'FDCP'],
            ['nombre' => 'Facultad de Ciencias Económicas, Administrativas y Contables', 'siglas' => 'FACEAC'],
        ];

        // Completar cada facultad con los campos faltantes
        foreach ($facultades as &$facultad) {
            $facultad['tipo'] = 'Facultad';
            $facultad['entidad_superior_id'] = null;
            $facultad['eliminado'] = false;
        }
        unset($facultad);

        DB::table('entidades')->insert($facultades);

        // 8. Insertar Escuelas
        // Cada escuela se asocia a su facultad (se busca el id de la facultad según la sigla)
        $escuelas = [
            ['nombre' => 'Biología - Biología', 'siglas' => 'BB', 'facultad' => 'FCB'],
            ['nombre' => 'Biología - Botánica', 'siglas' => 'BBT', 'facultad' => 'FCB'],
            ['nombre' => 'Biología - Microbiología - Parasitología', 'siglas' => 'BMP', 'facultad' => 'FCB'],
            ['nombre' => 'Biología - Pesquería', 'siglas' => 'BP', 'facultad' => 'FCB'],
            ['nombre' => 'Enfermería', 'siglas' => 'ENF', 'facultad' => 'FE'],
            ['nombre' => 'Medicina Humana', 'siglas' => 'MH', 'facultad' => 'FMH'],
            ['nombre' => 'Medicina Veterinaria', 'siglas' => 'MV', 'facultad' => 'FMV'],
            ['nombre' => 'Arquitectura', 'siglas' => 'ARQ', 'facultad' => 'FICSA'],
            ['nombre' => 'Ingeniería Civil', 'siglas' => 'IC', 'facultad' => 'FICSA'],
            ['nombre' => 'Ingeniería de Sistemas', 'siglas' => 'IS', 'facultad' => 'FICSA'],
            ['nombre' => 'Ingeniería en Computación e Informática', 'siglas' => 'ICI', 'facultad' => 'FACFYM'],
            ['nombre' => 'Estadística', 'siglas' => 'EST', 'facultad' => 'FACFYM'],
            ['nombre' => 'Física', 'siglas' => 'FIS', 'facultad' => 'FACFYM'],
            ['nombre' => 'Matemáticas', 'siglas' => 'MAT', 'facultad' => 'FACFYM'],
            ['nombre' => 'Ingeniería Electrónica', 'siglas' => 'IE', 'facultad' => 'FIME'],
            ['nombre' => 'Ingeniería Mecánica y Eléctrica', 'siglas' => 'IME', 'facultad' => 'FIME'],
            ['nombre' => 'Ingeniería Química', 'siglas' => 'IQ', 'facultad' => 'FIQIA'],
            ['nombre' => 'Ingeniería de Industrias Alimentarias', 'siglas' => 'IIA', 'facultad' => 'FIQIA'],
            ['nombre' => 'Agronomía', 'siglas' => 'AGRO', 'facultad' => 'FA'],
            ['nombre' => 'Ingeniería Agrícola', 'siglas' => 'IA', 'facultad' => 'FIA'],
            ['nombre' => 'Ingeniería Zootecnia', 'siglas' => 'IZ', 'facultad' => 'FIZ'],
            ['nombre' => 'Arqueología', 'siglas' => 'ARQ', 'facultad' => 'FCHSE'],
            ['nombre' => 'Arte con Especialidad en Artes Plásticas', 'siglas' => 'AAP', 'facultad' => 'FACHSE'],
            ['nombre' => 'Arte con Especialidad en Teatro', 'siglas' => 'AET', 'facultad' => 'FCHSE'],
            ['nombre' => 'Arte con Especialidad en Pedagogía Artística', 'siglas' => 'APA', 'facultad' => 'FACHSE'],
            ['nombre' => 'Arte con Especialidad en Música', 'siglas' => 'AMU', 'facultad' => 'FACHSE'],
            ['nombre' => 'Arte con Especialidad en Danzas', 'siglas' => 'ADA', 'facultad' => 'FACHSE'],
            ['nombre' => 'Ciencias de la Comunicación', 'siglas' => 'CC', 'facultad' => 'FACHSE'],
            ['nombre' => 'Psicología', 'siglas' => 'PSI', 'facultad' => 'FACHSE'],
            ['nombre' => 'Sociología', 'siglas' => 'SOC', 'facultad' => 'FACHSE'],
            ['nombre' => 'Educación Especialidad de Educación Inicial', 'siglas' => 'EEI', 'facultad' => 'FACHSE'],
            ['nombre' => 'Derecho', 'siglas' => 'DER', 'facultad' => 'FDCP'],
            ['nombre' => 'Ciencia Política', 'siglas' => 'CP', 'facultad' => 'FDCP'],
            ['nombre' => 'Administración', 'siglas' => 'ADM', 'facultad' => 'FCEAC'],
            ['nombre' => 'Comercio y Negocios Internacionales', 'siglas' => 'CNI', 'facultad' => 'FACEAC'],
            ['nombre' => 'Contabilidad', 'siglas' => 'CONT', 'facultad' => 'FACEAC'],
            ['nombre' => 'Economía', 'siglas' => 'ECO', 'facultad' => 'FACEAC'],
        ];

        foreach ($escuelas as $escuela) {
            // Buscamos el id de la facultad según la sigla
            $facultadId = DB::table('entidades')
                ->where('siglas', $escuela['facultad'])
                ->where('tipo', 'Facultad')
                ->value('id');

            if ($facultadId) {
                DB::table('entidades')->insert([
                    'nombre' => 'Dirección de Escuela ' . $escuela['nombre'],
                    'siglas' => $escuela['siglas'],
                    'tipo' => 'Escuela Profesional',
                    'entidad_superior_id' => $facultadId,
                    'eliminado' => false,
                ]);
            }
            if ($facultadId) {
                // Verificar si la Unidad de Posgrado ya existe
                $unidadPosgradoId = DB::table('entidades')
                    ->where('nombre', 'Unidad de Posgrado ' . $escuela['facultad'])
                    ->where('tipo', 'Unidad de Posgrado')
                    ->where('entidad_superior_id', $facultadId)
                    ->value('id');

                // Si no existe, la creamos
                if (!$unidadPosgradoId) {
                    $unidadPosgradoId = DB::table('entidades')->insertGetId([
                        'nombre' => 'Unidad de Posgrado ' . $escuela['facultad'],
                        'siglas' => 'UP-' . $escuela['facultad'],
                        'tipo' => 'Unidad de Posgrado',
                        'entidad_superior_id' => $facultadId,
                        'eliminado' => false,
                    ]);
                }
            }

        }

        // 9. Insertar subentidades para cada facultad
        $subentidades = [];
        foreach ($facultades as $facultad) {
            // Obtenemos el id de la facultad (por siglas)
            $facultadId = DB::table('entidades')
                ->where('siglas', $facultad['siglas'])
                ->where('tipo', 'Facultad')
                ->value('id');

            if ($facultadId) {
                $subentidades[] = [
                    'nombre' => 'Decanato ' . $facultad['siglas'],
                    'siglas' => 'D-' . $facultad['siglas'],
                    'tipo' => 'Decanato',
                    'entidad_superior_id' => $facultadId,
                    'eliminado' => false,
                ];
                $subentidades[] = [
                    'nombre' => 'Departamento Académico ' . $facultad['siglas'],
                    'siglas' => 'DA-' . $facultad['siglas'],
                    'tipo' => 'Departamento',
                    'entidad_superior_id' => $facultadId,
                    'eliminado' => false,
                ];
                $subentidades[] = [
                    'nombre' => 'Unidad de Investigación ' . $facultad['siglas'],
                    'siglas' => 'UI-' . $facultad['siglas'],
                    'tipo' => 'Unidad',
                    'entidad_superior_id' => $facultadId,
                    'eliminado' => false,
                ];
            }
        }

        if (!empty($subentidades)) {
            DB::table('entidades')->insert($subentidades);
        }
        $entidades = [
            ['nombre' => 'Decanatos', 'siglas' => 'DEC', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Direcciones de Escuelas', 'siglas' => 'DIREC', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Departamentos Académicos', 'siglas' => 'DEPA', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Decanatos, Direcciones de Escuelas', 'siglas' => 'DEC-DIREC', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Decanatos, Departamentos Académicos', 'siglas' => 'DEC-DEPA', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Direcciones de Escuelas, Departamentos Académicos', 'siglas' => 'DIREC-DEPA', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
            ['nombre' => 'Decanatos, Direcciones de Escuelas, Departamentos Académicos', 'siglas' => 'DEC-DIREC-DEPA', 'tipo' => 'Circular', 'entidad_superior_id' => null, 'eliminado' => 0],
        ];

        DB::table('entidades')->insert($entidades);

        // 2. Insertar órganos especiales
        DB::table('entidades')->insert([
            ['nombre' => 'Estudiante', 'siglas' => 'EST', 'tipo' => 'Otro', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Docente', 'siglas' => 'DCTE', 'tipo' => 'Otro', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Superintendencia Nacional de Educación Superior Universitaria', 'siglas' => 'SUNEDU', 'tipo' => 'Otro', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Ministerio de Educación', 'siglas' => 'MINEDU', 'tipo' => 'Otro', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Unidad de Planeamiento', 'siglas' => 'UPL', 'tipo' => 'Unidad', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Unidad Formuladora', 'siglas' => 'UFOR', 'tipo' => 'Unidad', 'entidad_superior_id' => null, 'eliminado' => false],
            ['nombre' => 'Unidad de Modernización', 'siglas' => 'UPL', 'tipo' => 'Unidad', 'entidad_superior_id' => null, 'eliminado' => false],

        ]);
    }
}