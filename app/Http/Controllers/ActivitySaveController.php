<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipApplication;
use App\Models\Scholarship;
use App\Models\ActivitySave;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\ActivityInformation;
use App\Models\ActivityRegister;

class ActivitySaveController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        if ($user->hasRole('admin') || $user->hasRole('officer')) {
            $saves = ActivitySave::all();
        } else {
            $saves = ActivitySave::where('user_id', $user->id)->get();
        }
        return Inertia::render('Admin/Activities/Saves/Index', [
            'saves' => $saves,
        ]);
    }
    public function create(Request $request): Response
    {
        $regis_id = $request->input('regis_id');
        // $this->authorize('create', ActivitySave::class);

        $activities = ActivityRegister::all();

        return Inertia::render('Admin/Activities/Saves/Create', [
            'regis_id' => $regis_id,
            'activities' => $activities,
        ]);
    }

    public function creates(Request $request): Response
    {
        $regis_id = $request->input('regis_id');
        $activities = ActivityRegister::all();
        $save = ActivitySave::all();


        return Inertia::render('Admin/Activities/Saves/Creates', [
            'regis_id' => $regis_id,
            'activities' => $activities,
            'save' => $save,

        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // $this->authorize('create', ActivitySave::class);
        $validated = $request->validate([
            'user_id' => 'exists:users,id',
            'regis_id' => 'exists:activity_registers,id',
            'date' => 'date',
            'title' => 'string',
            'fname' => 'string',
            'lname' => 'string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'institution' => 'string|max:255',
            'launch' => 'date_format:H:i',
            'end' => 'date_format:H:i',
            'location' => 'string',
            'years' => 'string',
            'hours' => 'integer',
            'result' => 'string|max:5',
            'suggestions' => 'nullable|string',
            'activity_name' => 'nullable|string',
            'check_date' => 'nullable|date',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $fileName = time() . '_file.' . $request->file('file')->extension();
            $request->file('file')->move(public_path('storage/files'), $fileName);
            $validated['file'] = $fileName;
        }

        // Create new ActivitySave
        ActivitySave::create($validated);

        return redirect()->route('activity_saves.index')->with('success', 'Activity saved successfully.');
    }

    public function show($id)

    {
        $save = ActivitySave::findOrFail($id);

        // ตรวจสอบสิทธิ์ของผู้ใช้ในการดูข้อมูล
        // $this->authorize('view', $save);
        $user = auth()->user(); // Get the authenticated user

        return Inertia::render('Admin/Activities/Saves/Show', [
            'save' => $save,
            'user' => $user

        ]);
    }


    public function editcheck(ActivitySave $save): Response
    {
        // $this->authorize('update', $save);
        $activity_registers = ActivityRegister::with('activitySaves')->get();

        return Inertia::render('Admin/Activities/Saves/Show', [
            'save' => $save,
            'activity_registers' => $activity_registers,
        ]);
    }

    public function updatecheck(Request $request, ActivitySave $save)
    {
        $request->validate([
            'result' => 'string|max:5',
            'suggestions' => 'nullable|string',
            'check_date' => 'nullable|date',
            'hours' => 'integer',
        ]);

        try {
            $save->update([
                'result' => $request->result,
                'suggestions' => $request->suggestions,
                'check_date' => $request->check_date,
                'hours' => $request->hours,
            ]);

            return redirect()->route('activity_saves.show', ['save' => $save->id])
                ->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }




    public function destroy(ActivitySave $save): RedirectResponse
    {
        $save->delete();
        return redirect()->route('activity_saves.index')->with('success', 'Activity deleted successfully.');
    }
    public function summary(Request $request)
    {
        $user = auth()->user();

        // เริ่มต้นการดึงข้อมูล
        $applications = ScholarshipApplication::all();
        $scholarships = Scholarship::all();
        $activities = ActivitySave::where('result', 1)->get();

        // ตรวจสอบการส่งชื่อที่ต้องการกรอง
        $filterName = $request->input('fname'); // รับค่า fname จาก request

        // หากผู้ใช้มีบทบาท scholar
        if ($user->hasRole('scholar')) {
            $applications = ScholarshipApplication::where('user_id', $user->id)->get();
            $activities = ActivitySave::where('user_id', $user->id)->get();
        }

        // ตรวจสอบว่า $activities ไม่เป็นค่าว่างก่อนจะทำการจัดกลุ่ม
        $academicYears = $activities->pluck('years')->unique()->sort()->values();

        // กรองชื่อก่อนการจัดกลุ่ม
        if ($filterName) {
            $activities = $activities->filter(function ($activity) use ($filterName) {
                return stripos($activity->fname, $filterName) !== false;
            });
        }

        $summary = $activities->groupBy('years')->map(function ($group) use ($applications) {
            return $group->groupBy('user_id')->map(function ($subGroup) use ($applications) { 
                $scholarApplication = $applications->where('user_id', $subGroup->first()->user_id)->first();
                $scholar_id = $scholarApplication ? $scholarApplication->scholar_id : null;
                $user = $subGroup->first()->user;
                $scholarType = $scholarApplication->scholar_type ?? 2; 
                $minHours = $user->hasRole('intlscholar') ? 20 : ($scholarType == 0 ? 100 : ($scholarType == 1 ? 50 : 0));

                return [
                    'user_id' => $subGroup->first()->user_id,
                    'fname' => $subGroup->first()->fname,
                    'lname' => $subGroup->first()->lname,
                    'years' => $subGroup->first()->years,
                    'total_hours' => $subGroup->sum('hours'),
                    'min_hours' => $minHours,
                    'scholarType' => $scholarType,
                    'title' => $subGroup->first()->title,
                    'scholar_id' => $scholar_id,
                ];
            });
        });

        return Inertia::render('Admin/Activities/Report', [
            'summary' => $summary,
            'academicYears' => $academicYears,
            'applications' => $applications,
            'scholarships' => $scholarships,
            'activities' => $activities,
        ]);
    }




    public function edit($id): Response
    {
        $activity_registers = ActivityRegister::with('activitySaves')->get();
        $save = ActivitySave::findOrFail($id);

        return Inertia::render('Admin/Activities/Saves/Edit', [
            'save' => $save,
            'activity_registers' => $activity_registers,
        ]);
    }
    public function update(Request $request, ActivitySave $save)
    {
        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'regis_id' => 'exists:activity_registers,id',
            'date' => 'date',
            'title' => 'string',
            'fname' => 'string',
            'lname' => 'string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'institution' => 'string|max:255',
            'launch' => 'date_format:H:i',
            'end' => 'date_format:H:i',
            'location' => 'string',
            'years' => 'string',
            'result' => 'string|max:5',
        ]);

        if ($request->hasFile('file')) {
            if ($save->file && file_exists(public_path('storage/files/' . $save->file))) {
                unlink(public_path('storage/files/' . $save->file));
            }

            $fileName = time() . '_file.' . $request->file('file')->extension();
            $request->file('file')->move(public_path('storage/files'), $fileName);
            $validatedData['file'] = $fileName;
        }

        $save->update($validatedData);

        return redirect()->route('activity_saves.index')->with('success', 'Activity updated successfully.');
    }
}
