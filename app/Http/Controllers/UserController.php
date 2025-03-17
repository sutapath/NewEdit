<?php

namespace App\Http\Controllers;

use App\Models\Student;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSharedResource;
use App\Models\User;
use App\Models\model_has_roles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

    class UserController extends Controller
    {
 
        public function getUsersWithRoles()
        {
            $users = User::with('roles')->get();
            return response()->json($users);
        }
        /**
         * Display a listing of the resource.
         */
    public function index(): Response
    {
        return Inertia::render('Admin/Users/UserIndex', [
            'users' => UserResource::collection(
                User::orderByDesc('created_at')->get() // เรียงจากใหม่ -> เก่า
            )
        ]);
    }

    
    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => RoleResource::collection(Role::all()),
            'permissions' => PermissionResource::collection(Permission::all())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
{
    // การตรวจสอบความถูกต้องของข้อมูลที่ได้รับ
    $request->validate([
        'name' => 'required|string|max:255',
        'title' => 'required|string|max:5',
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:' . User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'student_code' => 'required|string|max:10',
        'branch' => 'required|string|max:255',
        'faculty' => 'required|string|max:255',
        'phone' => 'required|string|max:15',
        'house_no' => 'required|string|max:100',
        'sub_district' => 'required|string|max:100',
        'district' => 'required|string|max:100',
        'province' => 'required|string|max:100',
        'zip_code' => 'required|string|max:10',
        'id_card' => 'required|string|max:15',
        'en_year' => 'required',
        'officer_code' => 'nullable|string|max:15',
    ]);

    // การสร้างผู้ใช้ใหม่ในฐานข้อมูล
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,            
        'title' => $request->title,
        'fname' => $request->fname,
        'lname' => $request->lname,
        'password' => Hash::make($request->password),
        'student_code' => $request->student_code,
        'branch' => $request->branch,
        'faculty' => $request->faculty,
        'phone' => $request->phone,
        'house_no' => $request->house_no,
        'sub_district' => $request->sub_district,
        'district' => $request->district,
        'province' => $request->province,
        'zip_code' => $request->zip_code,
        'en_year' => $request->en_year,
        'officer_code' => $request->officer_code,
        'id_card' => $request->id_card,
    ]);

    $user->syncRoles($request->input('roles.*.name'));
    $user->syncPermissions($request->input('permissions.*.name'));

    // การเปลี่ยนเส้นทางกลับไปยังหน้าแสดงรายชื่อผู้ใช้
    return to_route('users.index');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        $user->load(['roles' ]);
        return Inertia::render('Admin/Users/Edit', [
            'user' => UserResource::make($user),
            'roles' => RoleResource::collection(Role::all()),
            'permissions' => PermissionResource::collection(Permission::all())
        ]);
    }
 

    public function update(Request $request, User $user): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|max:255',
        // 'email' => 'required|string|email|max:255|' . Rule::unique('users', 'email')->ignore($user),
        // 'roles' => ['sometimes', 'array'],
        'permissions' => ['sometimes', 'array'],
        'student_code' => 'nullable|string|max:15',
        'title' => 'nullable|string|max:5',
        'fname' => 'nullable|string|max:255',
        'lname' => 'nullable|string|max:255',
        'birthday' => 'nullable|date',
        'phone' => 'nullable|string|max:15',
        'id_card' => 'nullable|string|max:15',
        'image' => 'nullable|string|max:255',
        'faculty' => 'nullable|string|max:255',
        'branch' => 'nullable|string|max:255',
        'en_year' => 'nullable',
        'house_no' => 'nullable|string|max:10',
        'village_name' => 'nullable|string|max:255',
        'sub_district' => 'nullable|string|max:255',
        'district' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'zip_code' => 'nullable|string|max:15',
        'officer_code' => 'nullable|string|max:15',
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'student_code' => $request->student_code,
        'title' => $request->title,
        'fname' => $request->fname,
        'lname' => $request->lname,
        'birthday' => $request->birthday,
        'phone' => $request->phone,
        'id_card' => $request->id_card,
        'image' => $request->image,
        'faculty' => $request->faculty,
        'branch' => $request->branch,
        'en_year' => $request->en_year,
        'house_no' => $request->house_no,
        'village_name' => $request->village_name,
        'sub_district' => $request->sub_district,
        'district' => $request->district,
        'province' => $request->province,
        'zip_code' => $request->zip_code,
        'officer_code' => $request->officer_code,
    ]);

    // $user->syncRoles($request->input('roles.name'));
    // $user->syncPermissions($request->input('permissions.name'));

    return back();
}


public function editscholar(User $user): Response
{
    $user->load(['roles']);
    return Inertia::render('Admin/Users/EditScholar', [
        'user' => UserResource::make($user),
        'roles' => RoleResource::collection(Role::all()),
    ]);
} 
  public function editscholar2(User $user): Response
    {
        $user->load(['roles']);
        return Inertia::render('Admin/Scholarships/AllStudents', [
            'user' => UserResource::make($user),
            'roles' => RoleResource::collection(Role::all()),
        ]);
    }
    public function updatescholar(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
        ]);

        $student = Student::where('citizen_id', $user->id_card)->first();

        if ($student) {
            $roles = $request->input('roles.*.name', []);

            // ตรวจสอบ role
            $type = null;
            $type_fund = null;

            if (in_array('student', $roles)) {
                $type = "0";
                $type_fund = "0";
            } elseif (in_array('scholar', $roles)) {
                $type = "0";
                $type_fund = "1";
            } elseif (in_array('intlscholar', $roles)) {
                $type = "1";
                $type_fund = "1";
            } 
            $user->update([
                'name' => $request->name ?? $user->name,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]); 
            $student->update([
                'type' => $type,
                'type_fund' => $type_fund,
            ]);
             
        }
        $user->syncRoles($request->input('roles.*.name'));
        $user->syncPermissions($request->input('permissions.*.name'));
        return back();
    }
 

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return to_route('users.index');
    }
}
