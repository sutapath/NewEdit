<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Models\Officer; 
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Hash; 

class OfficerController extends Controller
{
    /**
     * Display a listing of the officers.
     */
    public function index()
    {
        $officers = Officer::all(); // Fetch all officers from the database

        return Inertia::render('Admin/Users/Officers/Index', [
            'officers' => $officers
        ]); // Pass data to Inertia view
    }

    /**
     * Show the form for creating a new officer.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Officers/Create'); // Render the create form
    }

    /**
     * Store a newly created officer in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data with array rule
        $validated = $request->validate([
            'officers.*.citizen_id' => 'required|unique:officers,citizen_id|max:255',
            'officers.*.title' => 'required|max:255',
            'officers.*.fname' => 'required|max:255',
            'officers.*.lname' => 'required|max:255',
            'officers.*.code' => 'required|unique:officers|max:255',
        ]);

        // Loop ผ่านทุก officer และบันทึกลงฐานข้อมูล
        foreach ($validated['officers'] as $officerData) {
            // สร้าง officer ใหม่ในตาราง officers
            $officer = Officer::create([
                'citizen_id' => $officerData['citizen_id'],
                'title' => $officerData['title'],
                'fname' => $officerData['fname'],
                'lname' => $officerData['lname'],
                'code' => $officerData['code'],
            ]);

            // สร้างผู้ใช้ใหม่ในตาราง users
            User::create([
                'name' => $officerData['code'], // 'code' เป็น 'name'
                'password' => Hash::make($officerData['citizen_id']), // ใช้ citizen_id เป็น password หลังจาก hash
                'title' => $officerData['title'],
                'fname' => $officerData['fname'],
                'lname' => $officerData['lname'],
                'id_card' => $officerData['citizen_id'], // ใส่ citizen_id ลงใน id_card
                'role' => 2,

            ]);
        }

        return redirect()->route('officers.index')->with('success', 'Officers created successfully.');
    }

    /**
     * Display the specified officer.
     */
    public function show(Officer $officer)
    {
        return Inertia::render('Admin/Users/Officers/Show', [
            'officer' => $officer
        ]); // Pass data to Inertia view
    }

    /**
     * Show the form for editing the specified officer.
     */
    public function edit(Officer $officer)
    {
        return Inertia::render('Admin/Users/Officers/Edit', [
            'officer' => $officer
        ]); // Pass data to Inertia view
    }

    /**
     * Update the specified officer in storage.
     */
    public function update(Request $request, Officer $officer)
    {
        // Validate incoming data
        $validated = $request->validate([
            'citizen_id' => [
                'required',
                Rule::unique('officers', 'citizen_id')->ignore($officer->id),
                'max:255'
            ],
            'title' => 'required|max:255',
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'code' => [
                'required',
                Rule::unique('officers', 'code')->ignore($officer->id),
                'max:255'
            ],
        ], [
            'citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
            'code.required' => 'กรุณากรอกรหัสสอบ',
            'code.unique' => 'รหัสสอบนี้ถูกใช้ไปแล้ว',
        ]);

        // Update the officer
        $officer->update($validated);

        return redirect()->route('officers.index')->with('success', 'บันทึกสำเร็จแล้ว');
    }

    /**
     * Remove the specified officer from storage.
     */
    public function destroy(Officer $officer)
    {
        $officer->delete(); // Delete the officer

        return redirect()->route('officers.index')->with('success', 'Officer deleted successfully.');
    }
}
