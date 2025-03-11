<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Member;
use App\Models\Officer;
use App\Models\IntlScholar;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash; // Corrected import
use Illuminate\Validation\Rule; 
use Illuminate\Validation\Rules; 
use Inertia\Response;
use Illuminate\Validation\ValidationException;
class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index()
    {
        $members = Member::all(); // Fetch all members from the database

        return Inertia::render('Admin/Users/Members/Index', [
            'members' => $members
        ]); // Pass data to Inertia view
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Members/Create'); // Render the create form
    }

    /**
     * Store a newly created member in storage.
     */
    // public function store(Request $request)
    // { 
    //     $validated = $request->validate([
    //         'role' => 'required|in:admin,officer,scholar,student,member,intlscholar,manager',
    //         'members' => 'required|array|min:1',
    //         'members.*.citizen_id' => 'required|unique:users,id_card|max:13',
    //         'members.*.title' => 'required|max:255',
    //         'members.*.fname' => 'required|max:255',
    //         'members.*.lname' => 'required|max:255',
    //         'members.*.code' => 'nullable|max:255',
    //     ], [
    //         'role.required' => 'กรุณาเลือกบทบาท',
    //         'members.required' => 'ต้องมีข้อมูลสมาชิกอย่างน้อย 1 รายการ',
    //         'members.*.citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
    //         'members.*.code.required' => 'กรุณากรอกรหัสผู้ใช้',
    //         'members.*.citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
    //     ]);

    //     // Default user data
    //     $userData = [
    //         'name' => $validated['members'][0]['code'] ?? null,
    //         'password' => Hash::make($validated['members'][0]['citizen_id']),
    //         'title' => $validated['members'][0]['title'],
    //         'fname' => $validated['members'][0]['fname'],
    //         'lname' => $validated['members'][0]['lname'],
    //         'id_card' => $validated['members'][0]['citizen_id'],
    //         'type' => null,
    //         'type_fund' => null,
    //     ]; 
    //     switch ($validated['role']) {
    //         case 'student':
    //             $userData['type'] = "0";
    //             $userData['type_fund'] = "0";
    //             break;

    //         case 'scholar':
    //             $userData['type'] = "0";
    //             $userData['type_fund'] = "1";
    //             break;

    //         case 'intlscholar':
    //             $userData['type'] = "1";
    //             $userData['type_fund'] = "1";
    //             break;
    //     }

    //     if (in_array($validated['role'], ['student', 'scholar', 'intlscholar'])) {
    //         Student::create([
    //             'citizen_id' => $validated['members'][0]['citizen_id'],
    //             'title' => $validated['members'][0]['title'],
    //             'fname' => $validated['members'][0]['fname'],
    //             'lname' => $validated['members'][0]['lname'],
    //             'student_code' => $validated['members'][0]['code'],
    //         ]);

    //         $user = User::create($userData);
    //         $user->assignRole($validated['role']);
    //     } 
    //     if ($validated['role'] === 'member') {
    //         Member::create($validated['members'][0]);
    //     } elseif ($validated['role'] === 'officer') {
    //         Officer::create($validated['members'][0]);
    //     }

    //     return redirect()->route('users.index')->with('success', 'สร้างข้อมูลสำเร็จ');
    // }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,officer,scholar,student,member,intlscholar,manager',
            'members' => 'required|array|min:1',
            'members.*.citizen_id' => 'required|unique:users,id_card|max:13',
            'members.*.title' => 'required|max:255',
            'members.*.fname' => 'required|max:255',
            'members.*.lname' => 'required|max:255',
            'members.*.code' => 'nullable|max:255',
        ], [
            'role.required' => 'กรุณาเลือกบทบาท',
            'members.required' => 'ไม่พบข้อมูล',
            'members.*.citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'members.*.code.required' => 'กรุณากรอกรหัสผู้ใช้',
            'members.*.citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
            'members.*.citizen_id.max' => 'โปรดตรวจสอบข้อมูลหมายเลขประชาชน',
            'members.*.title.required' => 'กรุณากรอกคำนำหน้า',
            'members.*.fname.required' => 'กรุณากรอกชื่อ',
            'members.*.lname.required' => 'กรุณากรอกนามสกุล',
            'members.*.code.max' => 'รหัสผู้ใช้ไม่ถูกต้อง',
        ]);

        // Check if any error occurs, and return immediately on the first error.
        try {
            $validated = $request->validate([
                'role' => 'required|in:admin,officer,scholar,student,member,intlscholar,manager',
                'members' => 'required|array|min:1',
                'members.*.citizen_id' => 'required|unique:users,id_card|max:13',
                'members.*.title' => 'required|max:255',
                'members.*.fname' => 'required|max:255',
                'members.*.lname' => 'required|max:255',
                'members.*.code' => 'nullable|max:255',
            ], [
                'role.required' => 'กรุณาเลือกบทบาท',
                'members.required' => 'ไม่พบข้อมูล',
                'members.*.citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
                'members.*.code.required' => 'กรุณากรอกรหัสผู้ใช้',
                'members.*.citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
                'members.*.citizen_id.max' => 'โปรดตรวจสอบข้อมูลหมายเลขประชาชน',
                'members.*.title.required' => 'กรุณากรอกคำนำหน้า',
                'members.*.fname.required' => 'กรุณากรอกชื่อ',
                'members.*.lname.required' => 'กรุณากรอกนามสกุล',
                'members.*.code.max' => 'รหัสผู้ใช้ไม่ถูกต้อง',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'message' => 'พบข้อผิดพลาด กรุณาตรวจสอบข้อมูลที่กรอก',
            ], 422);
        }


        // Default user data
        $userData = [
            'name' => $validated['members'][0]['code'] ?? null,
            'password' => Hash::make($validated['members'][0]['citizen_id']),
            'title' => $validated['members'][0]['title'],
            'fname' => $validated['members'][0]['fname'],
            'lname' => $validated['members'][0]['lname'],
            'id_card' => $validated['members'][0]['citizen_id'],
            'type' => null,
            'type_fund' => null,
        ];

        switch ($validated['role']) {
            case 'student':
                $userData['type'] = "0";
                $userData['type_fund'] = "0";
                break;

            case 'scholar':
                $userData['type'] = "0";
                $userData['type_fund'] = "1";
                break;

            case 'intlscholar':
                $userData['type'] = "1";
                $userData['type_fund'] = "1";
                break;

            case 'admin':
                $userData['type'] = "1";
                break;

            case 'manager':
                $userData['type'] = "2";
                break;

            case 'officer':
                $userData['type'] = "3";
                break;
        }
        if (in_array($validated['role'], ['student', 'scholar', 'intlscholar'])) {
            Student::create([
                'citizen_id' => $validated['members'][0]['citizen_id'],
                'title' => $validated['members'][0]['title'],
                'fname' => $validated['members'][0]['fname'],
                'lname' => $validated['members'][0]['lname'],
                'student_code' => $validated['members'][0]['code'],
            ]);

            $user = User::create($userData);
            $user->assignRole($validated['role']);
        } 
        if ($validated['role'] === 'member') {
            Member::create($validated['members'][0]);
        } 
        if (in_array($validated['role'], ['officer', 'admin', 'manager'])) {
            Officer::create([
                'citizen_id' => $validated['members'][0]['citizen_id'],
                'title' => $validated['members'][0]['title'],
                'fname' => $validated['members'][0]['fname'],
                'lname' => $validated['members'][0]['lname'],
                'code' => $validated['members'][0]['code'],
                'type' => $userData['type'],
            ]);

            $user = User::create($userData);
            $user->assignRole($validated['role']);
        }

        return redirect()->route('users.index')->with('success', 'สร้างข้อมูลสำเร็จ');
    }


    public function show(Member $member)
    {
        return Inertia::render('Admin/Users/Members/Show', [
            'member' => $member
        ]); // Pass data to Inertia view
    } 
    public function edit(Member $member)
    {
        return Inertia::render('Admin/Users/Members/Edit', [
            'member' => $member
        ]); // Pass data to Inertia view
    } 
public function update(Request $request, Member $member)
{
    // Validate incoming data
    $validated = $request->validate([
        'citizen_id' => [
            'required',
            Rule::unique('members', 'citizen_id')->ignore($member->id),
            'max:255'
        ],
        'title' => 'required|max:255',
        'fname' => 'required|max:255',
        'lname' => 'required|max:255',
        'code' => [
            'required',
            Rule::unique('members', 'code')->ignore($member->id),
            'max:255'
        ],
    ], [
        'citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
        'citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
        'code.required' => 'กรุณากรอกรหัสสอบ',
        'code.unique' => 'รหัสสอบนี้ถูกใช้ไปแล้ว',
    ]);

    // Update the member
    $member->update($validated);

    return redirect()->route('members.index')->with('success', 'บันทึกสำเร็จแล้ว');

}

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete(); // Delete the member

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
