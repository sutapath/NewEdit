<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\DocumentRequest;

use App\Models\ScholarshipApplication;
use App\Models\ActivitySave;
use App\Models\UserLineNotify;
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
                $application->user = User::with('roles')->find($application->user_id);
                return $application;
            });
        $currentUser = Auth::user()->load('roles');

        return Inertia::render('Admin/Scholarships/ApplyScholars/Scholarstudents', [
            'applications' => $applications,
            'currentUser' => $currentUser,
            'scholarships' => $scholarships,
            'roles' => RoleResource::collection(Role::all()),
        ]);
    }
    public function AllStudents()
    {
        $user = auth()->user();
        $scholarships = Scholarship::all();
        $Notify = UserLineNotify::all();
        $applications = ScholarshipApplication::with('scholarship')
            ->where('result', 1)
            ->where('interview_score', '>=', 70)
            ->orderBy('interview_score', 'desc')
            ->get();
 
        $applicationsGrouped = $applications->groupBy('scholar_id');
        $finalApplications = collect();
        foreach ($applicationsGrouped as $scholarId => $groupedApplications) {
            $scholarshipLimit = $scholarships->where('id', $scholarId)->first()->limits;
            if ($groupedApplications->count() > $scholarshipLimit) {
                $groupedApplications = $groupedApplications->take($scholarshipLimit);
                $lastApplication = $groupedApplications->last();
                $sameScoreApplications = $applications->where('interview_score', $lastApplication->interview_score);
                $remainingApplications = $sameScoreApplications->filter(function ($app) use ($groupedApplications) {
                    return !$groupedApplications->contains('id', $app->id);
                });
                $groupedApplications = $groupedApplications->merge($remainingApplications)->unique('id');
            }
            $finalApplications = $finalApplications->merge($groupedApplications);
        } 
        return Inertia::render('Admin/Scholarships/ApplyScholars/AllStudents', [
            'Notify' => $Notify,
            'user' => $user,
            'scholarships' => $scholarships,
            'applications' => $finalApplications->unique('id'),
            'currentUser' => [
                'id' => $user->id,
                'roles' => $user->roles->pluck('name'), // ส่ง roles ไปด้วย
            ],
        ]);
    }
    public function interviewResults(Request $request)
    {
        try {
            $Results = $request->input('Results');
            $status = $request->input('status');
         
            if (!$Results) {
                return response()->json(['error' => 'No applications selected'], 400);
            }

            ScholarshipApplication::whereIn('id', $Results)->update([
                'Interview_results' => $status, 
            ]);

            return ;
        } catch (\Exception $e) {
            return ;
        }
    }


    public function UserCancel(Request $request)
    {
        try { 
            $ResultId = $request->input('Results');
            $status = $request->input('status'); 
            if (!$ResultId) {
                return response()->json(['error' => 'ไม่พบข้อมูลที่ต้องอัปเดต'], 400);
            } 
            ScholarshipApplication::where('id', $ResultId)
                ->update(['Interview_results' => $status]);
            return response()->json(['message' => '✅ อัปเดตผลสัมภาษณ์สำเร็จ!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => '❌ เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }


    public function index()
    {
        $applications = ScholarshipApplication::with('user')->get();
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
        try {
            $validated = $request->validated();
            $exists = ScholarshipApplication::where('user_id', $validated['user_id'])
                ->where('scholar_id', $validated['scholar_id'])
                ->exists();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'คุณได้สมัครทุนนี้ไปแล้ว']);
            }
            foreach (['imagefile', 'scholar_form', 'reg_form', 'fee_receipt', 'certificates', 'conduct_cert', 'portfolio', 'fam_cert', 'award_certs', 'leader_proof', 'gpa_image'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $fileName = time() . '_' . uniqid() . '.' . $request->file($fileField)->extension();
                    $request->file($fileField)->move(public_path('storage/files'), $fileName);
                    $validated[$fileField] = $fileName;
                }
            }
            ScholarshipApplication::create($validated);
            return redirect()->route('scholarship_applications.index')
                ->with('success', 'บันทึกข้อมูลสำเร็จ!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }


    // public function Contracts(Request $request): RedirectResponse
    // {
    //     try {
    //         $validated = $request->validate([
    //             'data_id' => 'required|exists:scholarship_applications,id',
    //             'scholarship_contract' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         ]);

    //         $scholarshipApplication = ScholarshipApplication::where('id', $request->data_id)->first();
    //         if ($request->hasFile('scholarship_contract')) {
    //             $fileName = time() . '_scholarship_contract.' . $request->file('scholarship_contract')->extension();
    //             $request->file('scholarship_contract')->move(public_path('storage/files'), $fileName);
    //             // เก็บชื่อไฟล์ลงในตัวแปร validated
    //             $validated['scholarship_contract'] = $fileName;
    //         }
    //         $scholarshipApplication->update($validated);
    //         return redirect()->back()->with('success', 'ข้อมูลถูกอัพเดตเรียบร้อย!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    //     }
    // }
    public function Contracts(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'data_id' => 'required|exists:scholarship_applications,id',
                'scholarship_contract' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
            $scholarshipApplication = ScholarshipApplication::where('id', $request->data_id)->first();
            if ($request->has('cancel_status') && $request->cancel_status == 3) {
                $validated['contract_suggestions'] = ''; // กำหนดค่าให้ contract_suggestions เป็นค่าว่าง
            }
            if ($request->hasFile('scholarship_contract')) {
                $fileName = time() . '_scholarship_contract.' . $request->file('scholarship_contract')->extension();
                $request->file('scholarship_contract')->move(public_path('storage/files'), $fileName);
                $validated['scholarship_contract'] = $fileName;
            }
            $scholarshipApplication->update($validated);

            return redirect()->back()->with('success', 'ข้อมูลถูกอัพเดตเรียบร้อย!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function deleteContracts($id)
    {
        try { 
            $scholarshipApplication = ScholarshipApplication::findOrFail($id);
            if ($scholarshipApplication->scholarship_contract && file_exists(public_path('storage/files/' . $scholarshipApplication->scholarship_contract))) {
                unlink(public_path('storage/files/' . $scholarshipApplication->scholarship_contract)); // ลบไฟล์จริง
            } 
            $scholarshipApplication->update(['scholarship_contract' => null]);

            return response()->json(['success' => 'ลบไฟล์เรียบร้อยแล้ว'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }


    public function interview()
    {
        $scholarships = Scholarship::all();
        $publicInfo = DocumentRequest::where('type', 0)
            ->whereNotNull('scholar_id') // เพิ่มเงื่อนไขนี้เพื่อตรวจสอบว่า scholar_id ไม่เป็น null  
            ->orderBy('created_at', 'desc')
            ->get();
        if (auth()->user()->hasRole(['admin', 'officer'])) {
            $applications = ScholarshipApplication::with('user')->get();
        } elseif (auth()->user()->hasRole(['student', 'member'])) {
            $applications = ScholarshipApplication::with('user')
                ->where('user_id', auth()->user()->id)
                ->get();
        }
        return Inertia::render('Admin/Scholarships/ApplyScholars/Interview', [
            'scholarships' => $scholarships,
            'applications' => $applications,
            'publicInfos' => $publicInfo,
        ]);
    }
    public function update(CreateScholarshipApplicationsRequest $request, $id)
    {
        $application = ScholarshipApplication::findOrFail($id);
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
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($application->$field && file_exists(public_path('storage/files/' . $application->$field))) {
                    unlink(public_path('storage/files/' . $application->$field));
                }
                $fileName = time() . '_' . $field . '.' . $request->file($field)->extension();
                $request->file($field)->move(public_path('storage/files'), $fileName);
                $validated[$field] = $fileName;
            } else {
                $validated[$field] = $application->$field;
            }
        }
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
        try {
            $application = ScholarshipApplication::findOrFail($id);
            $validated = $request->validated();
            $application->update($validated);

            return;
        } catch (\Exception $e) {
            return;
        }
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
    public function AddContract(ScholarshipApplication $application)
    {
        return Inertia::render('Admin/Scholarships/scholarContracts/AddContract', [
            'application' => $application,
            'user' => auth()->user(),  // ส่งข้อมูลผู้ใช้งานที่ล็อกอินอยู่
        ]);
    }


    public function interviewdetail(ScholarshipApplication $application): Response
    {
        return Inertia::render('Admin/Scholarships/ApplyScholars/Interviewdetail', [
            'application' => new ScholarshipApplicationsResource($application)
        ]);
    }
 

    public function ContractsDetail(ScholarshipApplication $application): Response
    {
        return Inertia::render('Admin/Scholarships/scholarContracts/ApplyDetail', [
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
