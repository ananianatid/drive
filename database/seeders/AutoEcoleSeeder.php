<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\AcademicClass;
use App\Models\Vehicule;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Exams;
use App\Models\License;
use App\Models\Period;
use App\Models\Presence;
use App\Models\Result;
use App\Models\IdentityCard;
use App\Models\Administrator;
use Carbon\Carbon;

class AutoEcoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les instructeurs
        $teacherUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->get();

        $teachers = [];
        foreach ($teacherUsers as $index => $user) {
            $teachers[] = Teacher::create([
                'user_id' => $user->id,
                'employee_number' => 'EMP' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'specialization' => ['Conduite défensive', 'Code de la route', 'Manœuvres'][$index % 3],
                'hire_date' => Carbon::now()->subYears(rand(2, 8)),
                'status' => 'active',
                'license_types' => ['A', 'B', 'C'],
                'experience_years' => rand(3, 15),
                'bio' => 'Instructeur expérimenté avec ' . rand(3, 15) . ' années d\'expérience.',
            ]);
        }

        // Créer les étudiants
        $studentUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })->get();

        $students = [];
        foreach ($studentUsers as $index => $user) {
            $students[] = Student::create([
                'user_id' => $user->id,
                'student_number' => 'STU' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'enrollment_date' => Carbon::now()->subMonths(rand(1, 6)),
                'status' => 'active',
                'license_type' => ['A', 'B', 'C'][rand(0, 2)],
                'progress_percentage' => rand(0, 100),
                'notes' => 'Étudiant motivé et assidu.',
            ]);
        }

        // Créer les véhicules
        $vehicules = [
            [
                'name' => 'Renault Clio',
                'model' => 'Clio V',
                'year' => 2020,
                'license_plate' => 'AB-123-CD',
                'color' => 'Blanc',
                'type' => 'car',
                'status' => 'available',
                'fuel_type' => 'gasoline',
                'transmission' => 'manual',
                'mileage' => 45000,
                'last_maintenance' => Carbon::now()->subMonths(2),
                'next_maintenance' => Carbon::now()->addMonths(1),
                'insurance_expiry' => Carbon::now()->addMonths(6),
                'registration_expiry' => Carbon::now()->addMonths(12),
            ],
            [
                'name' => 'Peugeot 208',
                'model' => '208',
                'year' => 2021,
                'license_plate' => 'EF-456-GH',
                'color' => 'Bleu',
                'type' => 'car',
                'status' => 'available',
                'fuel_type' => 'gasoline',
                'transmission' => 'automatic',
                'mileage' => 32000,
                'last_maintenance' => Carbon::now()->subMonths(1),
                'next_maintenance' => Carbon::now()->addMonths(2),
                'insurance_expiry' => Carbon::now()->addMonths(8),
                'registration_expiry' => Carbon::now()->addMonths(10),
            ],
            [
                'name' => 'Yamaha YBR 125',
                'model' => 'YBR 125',
                'year' => 2019,
                'license_plate' => 'IJ-789-KL',
                'color' => 'Rouge',
                'type' => 'motorcycle',
                'status' => 'available',
                'fuel_type' => 'gasoline',
                'transmission' => 'manual',
                'mileage' => 28000,
                'last_maintenance' => Carbon::now()->subMonths(3),
                'next_maintenance' => Carbon::now()->addMonths(1),
                'insurance_expiry' => Carbon::now()->addMonths(4),
                'registration_expiry' => Carbon::now()->addMonths(6),
            ],
        ];

        foreach ($vehicules as $vehiculeData) {
            Vehicule::create($vehiculeData);
        }

        // Créer les leçons
        $lessons = [
            [
                'title' => 'Code de la route - Bases',
                'description' => 'Introduction aux règles de base du code de la route',
                'content' => 'Cette leçon couvre les panneaux de signalisation, les priorités et les règles de base.',
                'duration' => 60,
                'difficulty_level' => 'beginner',
                'category' => 'théorique',
                'status' => 'active',
                'order' => 1,
                'prerequisites' => [],
                'objectives' => ['Comprendre les panneaux', 'Connaître les priorités'],
                'materials_needed' => ['Manuel de code', 'Vidéos explicatives'],
            ],
            [
                'title' => 'Manœuvres de stationnement',
                'description' => 'Apprentissage des techniques de stationnement',
                'content' => 'Créneau, bataille, stationnement en épi et en ligne.',
                'duration' => 90,
                'difficulty_level' => 'intermediate',
                'category' => 'pratique',
                'status' => 'active',
                'order' => 2,
                'prerequisites' => ['Code de la route - Bases'],
                'objectives' => ['Maîtriser le créneau', 'Réussir la bataille'],
                'materials_needed' => ['Véhicule', 'Cones de signalisation'],
            ],
            [
                'title' => 'Conduite en ville',
                'description' => 'Gestion de la circulation urbaine',
                'content' => 'Navigation dans les rues de la ville, gestion des intersections.',
                'duration' => 120,
                'difficulty_level' => 'intermediate',
                'category' => 'pratique',
                'status' => 'active',
                'order' => 3,
                'prerequisites' => ['Manœuvres de stationnement'],
                'objectives' => ['Naviguer en ville', 'Gérer les intersections'],
                'materials_needed' => ['Véhicule', 'GPS'],
            ],
        ];

        foreach ($lessons as $lessonData) {
            Lesson::create($lessonData);
        }

        // Créer les licences
        $licenses = [
            [
                'name' => 'Permis A (Moto)',
                'code' => 'A',
                'description' => 'Permis pour conduire des motocyclettes',
                'requirements' => ['Âge minimum 16 ans', 'Examen théorique', 'Examen pratique'],
                'validity_period' => 15,
                'minimum_age' => 16,
                'training_hours' => 20,
                'exam_requirements' => ['Code moto', 'Circulation'],
            ],
            [
                'name' => 'Permis B (Voiture)',
                'code' => 'B',
                'description' => 'Permis pour conduire des véhicules légers',
                'requirements' => ['Âge minimum 18 ans', 'Examen théorique', 'Examen pratique'],
                'validity_period' => 15,
                'minimum_age' => 18,
                'training_hours' => 20,
                'exam_requirements' => ['Code de la route', 'Circulation'],
            ],
            [
                'name' => 'Permis C (Poids lourd)',
                'code' => 'C',
                'description' => 'Permis pour conduire des véhicules lourds',
                'requirements' => ['Permis B', 'Âge minimum 21 ans', 'Examen médical'],
                'validity_period' => 5,
                'minimum_age' => 21,
                'training_hours' => 35,
                'exam_requirements' => ['Code poids lourd', 'Circulation'],
            ],
        ];

        foreach ($licenses as $licenseData) {
            License::create($licenseData);
        }

        // Créer les périodes
        $periods = [
            ['name' => 'Matin', 'start_time' => '08:00', 'end_time' => '12:00', 'duration' => 240],
            ['name' => 'Après-midi', 'start_time' => '14:00', 'end_time' => '18:00', 'duration' => 240],
            ['name' => 'Soirée', 'start_time' => '18:00', 'end_time' => '22:00', 'duration' => 240],
        ];

        foreach ($periods as $periodData) {
            Period::create($periodData);
        }

        // Créer les classes académiques
        $classes = [
            [
                'name' => 'Classe A - Moto Débutants',
                'description' => 'Formation complète pour le permis moto',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(1),
                'capacity' => 15,
                'status' => 'active',
                'teacher_id' => $teachers[0]->id,
            ],
            [
                'name' => 'Classe B - Voiture Avancés',
                'description' => 'Perfectionnement pour le permis voiture',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(2),
                'capacity' => 20,
                'status' => 'active',
                'teacher_id' => $teachers[1]->id,
            ],
            [
                'name' => 'Classe C - Poids Lourds',
                'description' => 'Formation pour le permis poids lourds',
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addMonths(3),
                'capacity' => 10,
                'status' => 'active',
                'teacher_id' => $teachers[2]->id,
            ],
        ];

        foreach ($classes as $classData) {
            AcademicClass::create($classData);
        }

        // Assigner les étudiants aux classes
        $classes = AcademicClass::all();
        foreach ($students as $index => $student) {
            $student->academic_class_id = $classes[$index % count($classes)]->id;
            $student->save();
        }

        // Créer les examens
        $exams = [
            [
                'title' => 'Examen Théorique - Code de la Route',
                'description' => 'Examen théorique du code de la route',
                'type' => 'theoretical',
                'duration' => 30,
                'passing_score' => 35,
                'max_score' => 40,
                'exam_date' => Carbon::now()->addDays(rand(7, 30)),
                'status' => 'active',
                'instructions' => '40 questions à choix multiples. 35 bonnes réponses minimum pour réussir.',
                'materials_allowed' => ['Calculatrice', 'Crayon'],
                'location' => 'Centre d\'examen - Paris',
            ],
            [
                'title' => 'Examen Pratique - Circulation',
                'description' => 'Examen pratique de conduite',
                'type' => 'practical',
                'duration' => 32,
                'passing_score' => 20,
                'max_score' => 31,
                'exam_date' => Carbon::now()->addDays(rand(10, 45)),
                'status' => 'active',
                'instructions' => '32 points à obtenir. 20 points minimum pour réussir.',
                'materials_allowed' => ['Véhicule d\'examen'],
                'location' => 'Circuit d\'examen - Paris',
            ],
        ];

        foreach ($exams as $examData) {
            Exams::create($examData);
        }

        // Créer des cours
        $vehicules = Vehicule::all();
        $lessons = Lesson::all();

        for ($i = 0; $i < 10; $i++) {
            $startTime = Carbon::now()->addDays(rand(1, 30))->setHour(rand(8, 18))->setMinute(0);
            $endTime = $startTime->copy()->addMinutes(rand(60, 120));

            Course::create([
                'academic_class_id' => $classes[rand(0, count($classes) - 1)]->id,
                'lesson_id' => $lessons[rand(0, count($lessons) - 1)]->id,
                'teacher_id' => $teachers[rand(0, count($teachers) - 1)]->id,
                'vehicule_id' => $vehicules[rand(0, count($vehicules) - 1)]->id,
                'title' => 'Cours ' . ($i + 1),
                'description' => 'Description du cours ' . ($i + 1),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'date' => $startTime->toDateString(),
                'duration' => $startTime->diffInMinutes($endTime),
                'status' => ['scheduled', 'active', 'completed'][rand(0, 2)],
                'max_students' => rand(2, 4),
                'notes' => 'Notes pour le cours ' . ($i + 1),
            ]);
        }

        // Créer des présences
        $courses = Course::all();
        foreach ($students as $student) {
            foreach ($courses->take(3) as $course) {
                Presence::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'date' => $course->date,
                    'status' => ['present', 'absent', 'late'][rand(0, 2)],
                    'arrival_time' => $course->start_time,
                    'departure_time' => $course->end_time,
                    'notes' => 'Présence enregistrée',
                ]);
            }
        }

        // Créer des résultats
        $exams = Exams::all();
        foreach ($students as $student) {
            foreach ($exams as $exam) {
                $score = rand(15, 40);
                $percentage = ($score / $exam->max_score) * 100;

                Result::create([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'score' => $score,
                    'max_score' => $exam->max_score,
                    'percentage' => $percentage,
                    'status' => $percentage >= $exam->passing_score ? 'passed' : 'failed',
                    'exam_date' => $exam->exam_date,
                    'notes' => 'Résultat de l\'examen',
                    'feedback' => $percentage >= $exam->passing_score ? 'Excellent travail !' : 'À améliorer',
                ]);
            }
        }

        // Créer des cartes d'identité
        foreach ($students as $student) {
            IdentityCard::create([
                'student_id' => $student->id,
                'card_number' => 'CARD' . str_pad($student->id, 4, '0', STR_PAD_LEFT),
                'issue_date' => Carbon::now()->subMonths(rand(1, 6)),
                'expiry_date' => Carbon::now()->addYears(2),
                'status' => 'active',
                'card_type' => 'student',
                'notes' => 'Carte d\'identité étudiante',
            ]);
        }

        // Créer un administrateur
        $adminUser = User::where('email', 'admin@auto-ecole.com')->first();
        if ($adminUser) {
            Administrator::create([
                'user_id' => $adminUser->id,
                'employee_number' => 'ADM001',
                'department' => 'Direction',
                'position' => 'Directeur',
                'hire_date' => Carbon::now()->subYears(5),
                'status' => 'active',
                'access_level' => 100,
                'notes' => 'Administrateur principal',
            ]);
        }

        $this->command->info('Données de l\'auto-école créées avec succès !');
    }
}
