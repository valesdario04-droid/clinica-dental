<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'reception' => 'Agenda citas y registra datos básicos de pacientes.',
            'doctor' => 'Registra y consulta sus servicios y atenciones.',
            'administrator' => 'Doctora dueña con acceso completo al sistema.',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(['name' => $name], ['description' => $description]);
        }

        $administrator = Role::where('name', 'administrator')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@clinica.test'],
            [
                'name' => 'Administradora Clínica',
                'role_id' => $administrator->id,
                'password' => 'Cambiar123!',
            ]
        );
    }
}
