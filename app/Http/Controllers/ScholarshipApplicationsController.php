<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Models\ScholarshipApplication;
use App\Models\ActivitySave;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Resources\ScholarshipApplicationsResource;
use App\Http\Requests\CreateScholarshipApplicationsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\Scholar;
use App\Models\ScholarshipRecipient;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;

class ScholarshipApplicationsController extends Controller
{
    public function scholarstudents()
    {
        $scholarships = Scholarship::all();
        $applications = ScholarshipApplication::with(['scholarship'])
            ->where('Interview_results', 1)
            ->get()
            ->map(function ($application) {
                // เอา user ที่ตรงกับ user_id ใน application มาแปะ
                $application->user = User::with('roles')->find($application->user_id);
                return $application;
            });

        // ดึงข้อมูลของผู้ใช้ที่ล็อกอิน พร้อม roles
        $currentUser = Auth::user()->load('roles');

        return Inertia::render('Admin/Scholarships/ApplyScholars/Scholarstudents', [
            'applications' => $applications, // ข้อมูลนักศึกษาทุน พร้อม user & roles
            'currentUser' => $currentUser, // ผู้ใช้ที่ล็อกอิน พร้อม role
            'scholarships' => $scholarships,
            'roles' => RoleResource::collection(Role::all()), // role ทั้งหมด
        ]);
    }


    public function index()
    {

        // ดึงข้อมูลการสมัครทั้งหมดพร้อมกับข้อมูลของผู้ใช้
        $applications = ScholarshipApplication::with('user')->get();

        // ส่งข้อมูลไปยัง Inertia
        return Inertia::render('Admin/Scholarships/ApplyScholars/AdminIndex', [
            'applications' => $applications,
            'currentUser' => Auth::user(),
        ]);
    }

    public function indexadmin()
    {


        $scholarships = Scholarship::all();
        $applications = ScholarshipApplication::with('user')->get();

        return Inertia::render('Admin/Scholarships/ApplyScholars/Index', [
            'applications' => $applications,
            'currentUser' => Auth::user(),
            'scholarships' => $scholarships,
        ]);
    }



    public function intanilcreate(Request $request)
    {
        $scholar_id = $request->query('scholar_id');
        $type = $request->query('type');

        return Inertia::render('Admin/Scholarships/ApplyScholars/IntanilCreate', [
            'scholar_id' => $scholar_id,
            'type' => $type,
        ]);
    }


    public function pertermcreate(Request $request): Response
    {
        $scholar_id = $request->input('scholar_id');

        return Inertia::render('Admin/Scholarships/ApplyScholars/Pertermcreate', [
            'scholar_id' => $scholar_id,
        ]);
    }

    public function store(CreateScholarshipApplicationsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        foreach (['imagefile', 'scholar_form', 'reg_form', 'fee_receipt', 'certificates', 'conduct_cert', 'portfolio', 'fam_cert', 'award_certs', 'leader_proof', 'gpa_image'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $fileName = time() . '_' . uniqid() . '.' . $request->file($fileField)->extension();
                $request->file($fileField)->move(public_path('storage/files'), $fileName);
                $validated[$fileField] = $fileName;
            }
        }
        ScholarshipApplication::create($validated);
        return redirect()->route('scholarship_applications.index');
    }

    public function interview()
    {
        // ดึงข้อมูลทุนการศึกษาทั้งหมด
        $scholarships = Scholarship::all();

        // ตรวจสอบบทบาทของผู้ใช้
        if (auth()->user()->hasRole(['admin', 'officer'])) {
            // ถ้าเป็น admin หรือ officer ให้ดึงข้อมูลทั้งหมดของ ScholarshipApplication
            $applications = ScholarshipApplication::with('user')->get();
        } elseif (auth()->user()->hasRole(['student', 'member'])) {
            // ถ้าเป็น student หรือ member ให้ดึงเฉพาะข้อมูลของผู้ใช้ที่ล็อกอินอยู่
            $applications = ScholarshipApplication::with('user')
                ->where('user_id', auth()->user()->id)
                ->get();
        }
        // ส่งข้อมูลไปยัง view หรือหน้า Inertia
        return Inertia::render('Admin/Scholarships/ApplyScholars/Interview', [
            'scholarships' => $scholarships,
            'applications' => $applications,
        ]);
    }

    public function update(CreateScholarshipApplicationsRequest $request, $id)
    {
        $application = ScholarshipApplication::findOrFail($id);

        // กำหนดชื่อฟิลด์ที่เกี่ยวข้องกับไฟล์
        $fileFields = [
            'imagefile',
            'scholar_form',
            'reg_form',
            'fee_receipt',
            'certificates',
            'conduct_cert',
            'portfolio',
            'fam_cert',
            'award_certs',
            'leader_proof',
            'gpa_image'
        ];

        $validated = $request->validated();

        // อัปเดตไฟล์
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // ถ้ามีไฟล์ใหม่
                if ($application->$field && file_exists(public_path('storage/files/' . $application->$field))) {
                    unlink(public_path('storage/files/' . $application->$field));
                }

                // บันทึกไฟล์ใหม่
                $fileName = time() . '_' . $field . '.' . $request->file($field)->extension();
                $request->file($field)->move(public_path('storage/files'), $fileName);
                $validated[$field] = $fileName;
            } else {
                // ถ้าไม่มีไฟล์ใหม่ ให้ใช้ข้อมูลเดิม
                $validated[$field] = $application->$field;
            }
        }

        // อัปเดตข้อมูลของการสมัคร
        $application->update($validated);

        return redirect()->route('scholarship_applications.index')->with('success', 'Application updated successfully.'); // เพิ่มข้อความยืนยัน
    }



    public function check(CreateScholarshipApplicationsRequest $request, $id)
    {
        $application = ScholarshipApplication::findOrFail($id);
        $validated = $request->validated();
        $application->update($validated);
        return;
    }

    public function interviewedit(CreateScholarshipApplicationsRequest $request, $id)
    {
        $application = ScholarshipApplication::findOrFail($id);
        $validated = $request->validated();

        // อัปเดตข้อมูล application
        $application->update($validated);
        $application->updateBasedOnInterviewResults();
        return;
    }

    public function intanil($id)
    {
        $application = ScholarshipApplication::findOrFail($id);
        return Inertia::render('Admin/Scholarships/ApplyScholars/IntanilEdit', [
            'application' => $application
        ]);
    }
    public function perterm($id)
    {
        $application = ScholarshipApplication::findOrFail($id);
        return Inertia::render('Admin/Scholarships/ApplyScholars/PertermEdit', [
            'application' => $application
        ]);
    }

    public function show(ScholarshipApplication $application): Response
    {

        return Inertia::render('Admin/Scholarships/ApplyScholars/Show', [
            'application' => new ScholarshipApplicationsResource($application)
        ]);
    }

    public function interviewdetail(ScholarshipApplication $application): Response
    {

        return Inertia::render('Admin/Scholarships/ApplyScholars/Interviewdetail', [
            'application' => new ScholarshipApplicationsResource($application)
        ]);
    }


    public function destroy(ScholarshipApplication $application): RedirectResponse
    {
        $application->delete();
        return back();
    }

    public function report()
    {
        $applications = ScholarshipApplication::where('interview_results', '1')->get();
        $scholarships = Scholarship::all();
        $activities = ActivitySave::where('result', 1)->get();

        if (auth()->user()->hasRole('scholar')) {
            $applications = ScholarshipApplication::where('user_id', auth()->user()->id)->get();
            $activities = ActivitySave::where('user_id', auth()->user()->id)->get();
        }

        return Inertia::render('Admin/Activities/Report', [
            'applications' => $applications,
            'scholarships' => $scholarships,
            'activities' => $activities,
        ]);
    }



    public function userSaves($user_id)
    {
        $saves = ActivitySave::where('user_id', $user_id)->get();

        return Inertia::render('Admin/Activities/Saves/UserSaves', [
            'saves' => $saves,
            'user_id' => $user_id,
        ]);
    }

    public function secoundreport()
    {
        $applications = ScholarshipApplication::with('user')
            ->where('Interview_results', 1)
            ->get();
        $applicationsByType = [
            'type_0' => $applications->where('scholar_type', 0),
            'type_1' => $applications->where('scholar_type', 1),
        ];
        $countByType = [
            'type_0_count' => $applicationsByType['type_0']->count(),
            'type_1_count' => $applicationsByType['type_1']->count(),
        ];
        $totalFees = $applications->where('Interview_results', 1)->sum('tuition_fee');

        return Inertia::render('Admin/Scholarships/Report', [
            'applications' => $applications,
            'applicationsByType' => $applicationsByType,
            'totalFees' => $totalFees,
            'countByType' => $countByType,
        ]);
    }
}
