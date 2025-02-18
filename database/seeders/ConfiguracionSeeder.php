<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configuraciones')->insert([
            'clave' => 'numero_oficio_inicio',
            'valor' => '1',  // El valor inicial de los números de oficio
        ]);
    }
}