<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; 
use App\Models\Student; 
use App\Models\Member; 
use App\Models\Officer; 
use App\Models\Scholar; 
use App\Models\Intlscholar; 
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{ 
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate ข้อมูลที่กรอกเข้ามา
        $request->validate([
            'title' => 'required|string|max:5',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'id_card' => 'required|string|size:13|unique:users,id_card',
            'phone' => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/'],
            'name' => 'required|string|min:4|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'id_card.required' => 'กรุณากรอกหมายเลขบัตรประชาชน',
            'id_card.size' => 'โปรดตรวจสอบหมายเลขบัตรประชาชน',
            'id_card.unique' => 'หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว', // เพิ่มข้อความแจ้งเตือน
            'phone.required' => 'กรุณากรอกหมายเลขโทรศัพท์',
            'phone.size' => 'โปรดตรวจสอบหมายเลขโทรศัพท์',
            'phone.regex' => 'กรุณาระบบเป็นตัวเลข',
            'name.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'name.min' => 'ชื่อผู้ใช้งานต้องมีความยาวไม่น้อยกว่า 4 ตัวอักษร',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'กรุณากรอกอีเมลที่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้แล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ]);

        $student = Student::where('citizen_id', $request->id_card)->first();
        $officer = Officer::where('citizen_id', $request->id_card)->first();
        $scholar = Scholar::where('citizen_id', $request->id_card)->first();
        $intlscholar = Intlscholar::where('citizen_id', $request->id_card)->first();
        $member = Member::where('citizen_id', $request->id_card)->first();
        $user = User::create([
            'title' => $request->title,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'id_card' => $request->id_card,
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]); 
        if ($student) {
            $user->assignRole('student');
        } elseif ($officer) {
            $user->assignRole('officer');
        } elseif ($scholar) {
            $user->assignRole('scholar');
        } elseif ($intlscholar) {
            $user->assignRole('intlscholar');
        } else { 
            $user->assignRole('member'); 
            Member::create([
                'citizen_id' => $request->id_card,
                'title' => $request->title,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'code' => null,
            ]);
        } 
        Auth::login($user); 
        return redirect(RouteServiceProvider::HOME);
    }



}
