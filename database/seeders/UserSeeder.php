<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles de base
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrateur',
            'description' => 'Accès complet à toutes les fonctionnalités',
            'is_active' => true,
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher',
            'display_name' => 'Instructeur',
            'description' => 'Gestion des cours et des étudiants',
            'is_active' => true,
        ]);

        $studentRole = Role::create([
            'name' => 'student',
            'display_name' => 'Étudiant',
            'description' => 'Accès aux cours et aux résultats',
            'is_active' => true,
        ]);

        // Créer les permissions de base
        $permissions = [
            ['name' => 'users.view', 'display_name' => 'Voir les utilisateurs', 'module' => 'users', 'action' => 'view'],
            ['name' => 'users.create', 'display_name' => 'Créer des utilisateurs', 'module' => 'users', 'action' => 'create'],
            ['name' => 'users.edit', 'display_name' => 'Modifier les utilisateurs', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'users.delete', 'display_name' => 'Supprimer les utilisateurs', 'module' => 'users', 'action' => 'delete'],
            ['name' => 'students.view', 'display_name' => 'Voir les étudiants', 'module' => 'students', 'action' => 'view'],
            ['name' => 'students.create', 'display_name' => 'Créer des étudiants', 'module' => 'students', 'action' => 'create'],
            ['name' => 'students.edit', 'display_name' => 'Modifier les étudiants', 'module' => 'students', 'action' => 'edit'],
            ['name' => 'students.delete', 'display_name' => 'Supprimer les étudiants', 'module' => 'students', 'action' => 'delete'],
            ['name' => 'teachers.view', 'display_name' => 'Voir les instructeurs', 'module' => 'teachers', 'action' => 'view'],
            ['name' => 'teachers.create', 'display_name' => 'Créer des instructeurs', 'module' => 'teachers', 'action' => 'create'],
            ['name' => 'teachers.edit', 'display_name' => 'Modifier les instructeurs', 'module' => 'teachers', 'action' => 'edit'],
            ['name' => 'teachers.delete', 'display_name' => 'Supprimer les instructeurs', 'module' => 'teachers', 'action' => 'delete'],
            ['name' => 'courses.view', 'display_name' => 'Voir les cours', 'module' => 'courses', 'action' => 'view'],
            ['name' => 'courses.create', 'display_name' => 'Créer des cours', 'module' => 'courses', 'action' => 'create'],
            ['name' => 'courses.edit', 'display_name' => 'Modifier les cours', 'module' => 'courses', 'action' => 'edit'],
            ['name' => 'courses.delete', 'display_name' => 'Supprimer les cours', 'module' => 'courses', 'action' => 'delete'],
            ['name' => 'vehicules.view', 'display_name' => 'Voir les véhicules', 'module' => 'vehicules', 'action' => 'view'],
            ['name' => 'vehicules.create', 'display_name' => 'Créer des véhicules', 'module' => 'vehicules', 'action' => 'create'],
            ['name' => 'vehicules.edit', 'display_name' => 'Modifier les véhicules', 'module' => 'vehicules', 'action' => 'edit'],
            ['name' => 'vehicules.delete', 'display_name' => 'Supprimer les véhicules', 'module' => 'vehicules', 'action' => 'delete'],
            ['name' => 'exams.view', 'display_name' => 'Voir les examens', 'module' => 'exams', 'action' => 'view'],
            ['name' => 'exams.create', 'display_name' => 'Créer des examens', 'module' => 'exams', 'action' => 'create'],
            ['name' => 'exams.edit', 'display_name' => 'Modifier les examens', 'module' => 'exams', 'action' => 'edit'],
            ['name' => 'exams.delete', 'display_name' => 'Supprimer les examens', 'module' => 'exams', 'action' => 'delete'],
            ['name' => 'results.view', 'display_name' => 'Voir les résultats', 'module' => 'results', 'action' => 'view'],
            ['name' => 'results.create', 'display_name' => 'Créer des résultats', 'module' => 'results', 'action' => 'create'],
            ['name' => 'results.edit', 'display_name' => 'Modifier les résultats', 'module' => 'results', 'action' => 'edit'],
            ['name' => 'results.delete', 'display_name' => 'Supprimer les résultats', 'module' => 'results', 'action' => 'delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Assigner toutes les permissions au rôle admin
        $adminRole->permissions()->attach(Permission::all());

        // Assigner les permissions spécifiques aux instructeurs
        $teacherPermissions = Permission::whereIn('name', [
            'students.view', 'students.edit',
            'courses.view', 'courses.create', 'courses.edit',
            'vehicules.view',
            'exams.view', 'exams.create', 'exams.edit',
            'results.view', 'results.create', 'results.edit'
        ])->get();
        $teacherRole->permissions()->attach($teacherPermissions);

        // Assigner les permissions spécifiques aux étudiants
        $studentPermissions = Permission::whereIn('name', [
            'courses.view',
            'exams.view',
            'results.view'
        ])->get();
        $studentRole->permissions()->attach($studentPermissions);

        // Créer un utilisateur administrateur
        $adminUser = User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@auto-ecole.com',
            'password' => Hash::make('password'),
            'phone' => '+33 1 23 45 67 89',
            'address' => '123 Rue de la Paix, 75001 Paris',
            'birth_date' => '1985-03-15',
        ]);

        $adminUser->roles()->attach($adminRole);

        // Créer des utilisateurs instructeurs
        $teachers = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@auto-ecole.com',
                'phone' => '+33 1 23 45 67 90',
                'address' => '456 Avenue des Champs, 75008 Paris',
                'birth_date' => '1980-07-22',
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@auto-ecole.com',
                'phone' => '+33 1 23 45 67 91',
                'address' => '789 Boulevard Saint-Germain, 75006 Paris',
                'birth_date' => '1988-11-10',
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre.durand@auto-ecole.com',
                'phone' => '+33 1 23 45 67 92',
                'address' => '321 Rue de Rivoli, 75001 Paris',
                'birth_date' => '1975-05-18',
            ],
        ];

        foreach ($teachers as $teacherData) {
            $teacherUser = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make('password'),
                'phone' => $teacherData['phone'],
                'address' => $teacherData['address'],
                'birth_date' => $teacherData['birth_date'],
            ]);

            $teacherUser->roles()->attach($teacherRole);
        }

        // Créer des utilisateurs étudiants
        $students = [
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie.bernard@email.com',
                'phone' => '+33 6 12 34 56 78',
                'address' => '123 Rue de la République, 75011 Paris',
                'birth_date' => '2000-09-15',
            ],
            [
                'name' => 'Lucas Petit',
                'email' => 'lucas.petit@email.com',
                'phone' => '+33 6 12 34 56 79',
                'address' => '456 Avenue de la Liberté, 75012 Paris',
                'birth_date' => '1998-12-03',
            ],
            [
                'name' => 'Emma Roux',
                'email' => 'emma.roux@email.com',
                'phone' => '+33 6 12 34 56 80',
                'address' => '789 Boulevard de la Villette, 75019 Paris',
                'birth_date' => '2002-04-28',
            ],
            [
                'name' => 'Thomas Moreau',
                'email' => 'thomas.moreau@email.com',
                'phone' => '+33 6 12 34 56 81',
                'address' => '321 Rue de Belleville, 75020 Paris',
                'birth_date' => '1995-08-12',
            ],
            [
                'name' => 'Léa Simon',
                'email' => 'lea.simon@email.com',
                'phone' => '+33 6 12 34 56 82',
                'address' => '654 Avenue Jean Jaurès, 75019 Paris',
                'birth_date' => '2001-01-25',
            ],
        ];

        foreach ($students as $studentData) {
            $studentUser = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'phone' => $studentData['phone'],
                'address' => $studentData['address'],
                'birth_date' => $studentData['birth_date'],
            ]);

            $studentUser->roles()->attach($studentRole);
        }

        $this->command->info('Utilisateurs, rôles et permissions créés avec succès !');
    }
}
