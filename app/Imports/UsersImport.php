<?php
namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // ตรวจสอบว่า student_code หรือ id_card ว่างหรือไม่
        if (empty($row['student_code']) || empty($row['id_card'])) {
            return null; // ข้ามแถวนี้หากไม่มี student_code หรือ id_card
        }

        // ตรวจสอบว่ามี User นี้อยู่แล้วหรือไม่ โดยใช้ student_code หรือ id_card
        $existingUser = User::where('student_code', $row['student_code'])
                            ->orWhere('id_card', $row['id_card'])
                            ->first();

        if (!$existingUser) {
            // สร้าง User ใหม่
            $user = User::create([
                'title'        => $row['title'] ?? null,
                'fname'        => $row['fname'] ?? null,
                'lname'        => $row['lname'] ?? null,
                'id_card'      => $row['id_card'] ?? null,
                'name'         => $row['fname'] ?? null,
                'student_code' => $row['student_code'] ?? null,
                'password'     => Hash::make($row['id_card']), // สร้างรหัสผ่านที่เข้ารหัส
                'username'     => $row['student_code'], // ใช้ student_code เป็น username
                'role'         => 4, // กำหนด role เป็น 4
            ]);

            // ตรวจสอบว่ามี Student นี้อยู่แล้วหรือไม่
            $existingStudent = Student::where('student_code', $row['student_code'])
                                      ->orWhere('citizen_id', $row['id_card'])
                                      ->first();

            if (!$existingStudent) {
                // สร้าง Student ใหม่
                Student::create([
                    'citizen_id'  =>  $user->id_card ?? null,
                ]);
            }
        }
    }
}
