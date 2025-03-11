<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Response;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();

        return Inertia::render('Admin/Users/Students/Index', [
            'students' => $students
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Students/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'students.*.citizen_id' => 'required|unique:students,citizen_id|max:255',
            'students.*.title' => 'required|max:255',
            'students.*.fname' => 'required|max:255',
            'students.*.lname' => 'required|max:255',
            'students.*.student_code' => 'required|unique:students,student_code|max:255',
        ], [
            'students.*.citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'students.*.citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
            'students.*.citizen_id.max' => 'หมายเลขบัตรประชาชนห้ามยาวเกิน 255 ตัวอักษร',
            'students.*.title.required' => 'กรุณากรอกคำนำหน้าชื่อ',
            'students.*.fname.required' => 'กรุณากรอกชื่อ',
            'students.*.lname.required' => 'กรุณากรอกนามสกุล',
            'students.*.student_code.required' => 'กรุณากรอกรหัสสอบ',
            'students.*.student_code.unique' => 'รหัสสอบนี้ถูกใช้ไปแล้ว',
            'students.*.student_code.max' => 'รหัสสอบห้ามยาวเกิน 255 ตัวอักษร',
        ]);

        foreach ($validated['students'] as $studentData) {
            $student = Student::create([
                'citizen_id' => $studentData['citizen_id'],
                'title' => $studentData['title'],
                'fname' => $studentData['fname'],
                'lname' => $studentData['lname'],
                'student_code' => $studentData['student_code'],
            ]);

            User::create([
                'name' => $studentData['student_code'],
                'password' => Hash::make($studentData['citizen_id']),
                'title' => $studentData['title'],
                'fname' => $studentData['fname'],
                'lname' => $studentData['lname'],
                'id_card' => $studentData['citizen_id'],
            ]);
        $student->assignRole('student');

        }

        return redirect()->route('students.index')->with('success', 'Students created successfully.');
    }

    public function show(Student $student)
    {
        return Inertia::render('Admin/Users/Students/Show', [
            'student' => $student
        ]);
    }

    public function edit(Student $student)
    {
        return Inertia::render('Admin/Users/Students/Edit', [
            'student' => $student
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'citizen_id' => [
                'required',
                Rule::unique('students', 'citizen_id')->ignore($student->id),
                'max:255'
            ],
            'title' => 'required|max:255',
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'student_code' => [
                'required',
                Rule::unique('students', 'student_code')->ignore($student->id),
                'max:255'
            ],
        ], [
            'citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
            'student_code.required' => 'กรุณากรอกรหัสสอบ',
            'student_code.unique' => 'รหัสสอบนี้ถูกใช้ไปแล้ว',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'บันทึกสำเร็จแล้ว');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
