<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Contracts\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::with(['user', 'academicClass'])
            ->orderByDesc('id')
            ->paginate(12);

        return view('students.index', compact('students'));
    }

    public function show(Student $student): View
    {
        $student->load([
            'user',
            'academicClass',
            'results.exam',
            'presences.course',
            'identityCard',
        ]);

        $latestIdentityCard = $student->identityCard()->latest('issue_date')->first();

        return view('students.show', [
            'student' => $student,
            'latestIdentityCard' => $latestIdentityCard,
        ]);
    }
}

