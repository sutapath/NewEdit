<?php

namespace App\Http\Controllers;

use App\Models\IntlScholar;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\Rule;

class IntlScholarController extends Controller
{
public function index()
{
    $intlScholars = IntlScholar::all()->map(function ($scholar) {
        return [
            'id' => $scholar->id,
            'citizen_id' => $scholar->citizen_id,
            'title' => $scholar->title ?? '',
            'fname' => $scholar->fname ?? '',
            'lname' => $scholar->lname ?? '',
            'code' => $scholar->code ?? '',
            'created_at' => $scholar->created_at,
            'updated_at' => $scholar->updated_at,
        ];
    });

    return Inertia::render('Admin/Users/IntlScholars/Index', [
        'intlScholars' => $intlScholars
    ]);
}

    public function create()
    {
        return Inertia::render('Admin/Users/IntlScholars/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intlScholars.*.citizen_id' => 'required|unique:intlscholars,citizen_id|max:255',
            'intlScholars.*.title' => 'required|max:255',
            'intlScholars.*.fname' => 'required|max:255',
            'intlScholars.*.lname' => 'required|max:255',
            'intlScholars.*.code' => 'required|unique:intlscholars,code|max:255',
        ], [
            'intlScholars.*.citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'intlScholars.*.citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
            'intlScholars.*.citizen_id.max' => 'กรุณาหมายเลขบัตรประชาชน',
            'intlScholars.*.title.required' => 'กรุณากรอกคำนำหน้าชื่อ',
            'intlScholars.*.fname.required' => 'กรุณากรอกชื่อ',
            'intlScholars.*.lname.required' => 'กรุณากรอกนามสกุล',
            'intlScholars.*.code.required' => 'กรุณากรอกรหัสนักศึกษา',
            'intlScholars.*.code.unique' => 'รหัสนักศึกษานี้ถูกใช้ไปแล้ว',
            'intlScholars.*.code.max' => 'กรุณารหัสนักศึกษา',
        ]);

        foreach ($validated['intlScholars'] as $scholarData) {
            $scholar = IntlScholar::create([
                'citizen_id' => $scholarData['citizen_id'],
                'title' => $scholarData['title'],
                'fname' => $scholarData['fname'],
                'lname' => $scholarData['lname'],
                'code' => $scholarData['code'],
            ]);

            User::create([
                'name' => $scholarData['code'],
                'password' => Hash::make($scholarData['citizen_id']),
                'title' => $scholarData['title'],
                'fname' => $scholarData['fname'],
                'lname' => $scholarData['lname'],
                'id_card' => $scholarData['citizen_id'],
                'role' => 6,
            ]);
        }

        return redirect()->route('intlScholars.index')->with('success', 'สร้างข้อมูลสำเร็จ');
    }

    public function show(IntlScholar $intlScholar)
    {
        return Inertia::render('Admin/Users/IntlScholars/Show', [
            'intlScholar' => $intlScholar
        ]);
    }

    public function edit(IntlScholar $intlScholar)
    {
        return Inertia::render('Admin/Users/IntlScholars/Edit', [
            'intlScholar' => $intlScholar
        ]);
    }

    public function update(Request $request, IntlScholar $intlScholar)
    {
        $validated = $request->validate([
            'citizen_id' => [
                'required',
                Rule::unique('intlscholars', 'citizen_id')->ignore($intlScholar->id),
                'max:255'
            ],
            'title' => 'required|max:255',
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'code' => [
                'required',
                Rule::unique('intlscholars', 'code')->ignore($intlScholar->id),
                'max:255'
            ],
        ], [
            'citizen_id.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'citizen_id.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว',
            'code.required' => 'กรุณากรอกรหัสนักศึกษา',
            'code.unique' => 'รหัสนักศึกษานี้ถูกใช้ไปแล้ว',
        ]);

        $intlScholar->update($validated);

        return redirect()->route('intlScholars.index')->with('success', 'บันทึกสำเร็จแล้ว');
    }

    public function destroy(IntlScholar $intlScholar)
    {
        $intlScholar->delete();

        return redirect()->route('intlScholars.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
