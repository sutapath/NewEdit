<?php
// app/Http/Controllers/ActivityRegisterController.php

namespace App\Http\Controllers;

use App\Models\ActivityRegister;
use App\Models\ActivitySave;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\ActivityInformation;
use Illuminate\Support\Facades\Auth;

class ActivityRegisterController extends Controller
{
    public function index(): Response
{ 
    $currentUser = Auth::user();

    if ($currentUser->hasRole('admin') || $currentUser->hasRole('officer')) { 
        $registrations = ActivityRegister::with(['user', 'activity'])->get();
        $activities = ActivityInformation::all();
        $activitySaves = ActivitySave::all(); 
    } elseif ($currentUser->hasRole('scholar') || $currentUser->hasRole('intlscholar')) { 
        $registrations = ActivityRegister::with(['user', 'activity'])
            ->where('user_id', $currentUser->id)
            ->get();
        $activities = ActivityInformation::all();
        $activitySaves = ActivitySave::where('user_id', $currentUser->id)->get(); 
    } else { 
        abort(403, 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้');
    }

    // ส่งข้อมูลไปยัง Vue Component ผ่าน Inertia
    return Inertia::render('Admin/Activities/Registrations/Index', [
        'currentUser' => $currentUser,
        'registrations' => $registrations,
        'activities' => $activities,
        'activitySaves' => $activitySaves,
    ]);
}


 


    public function create(Request $request): Response
    {
        // $this->authorize('create', ActivityRegister::class);

        $activity_id = $request->input('activity_id');
        $activities = ActivityInformation::all();  
    
        return Inertia::render('Admin/Activities/Registrations/Create', [
            'activity_id' => $activity_id,
            'currentUser' => Auth::user(),
            'activities' => $activities,
        ]);
    }

    public function store(Request $request)
    {
        // ตรวจสอบจำนวนผู้ลงทะเบียนในกิจกรรมนี้
        $activity = ActivityInformation::find($request->activity_id);
        $currentRegistrations = ActivityRegister::where('activity_id', $request->activity_id)->count();

        // ตรวจสอบว่าจำนวนที่ลงทะเบียนเกินขีดจำกัดหรือไม่
        if ($currentRegistrations >= $activity->limits) {
            return back()->withErrors(['limits' => 'จำนวนผู้เข้าร่วมกิจกรรมเต็มแล้ว!']);
        }

        // สร้างการลงทะเบียนใหม่
        ActivityRegister::create([
            'user_id' => $request->user_id,
            'activity_id' => $request->activity_id,
            'title' => $request->title,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'regis_date' => $request->regis_date,
        ]);

        return redirect()->route('activities.index')->with('success', 'ลงทะเบียนกิจกรรมสำเร็จ!');
    }
    
    public function destroy(ActivityRegister $registration): RedirectResponse
    {
        // $this->authorize('delete', $registration);
    
        $registration->delete(); 
        return redirect()->route('activity_registrations.index')->with('success', 'Activity updated successfully.');


    } 
public function updatecheck(Request $request, $registration)
{
    $registration = ActivityRegister::findOrFail($registration);

    // Validate incoming request
    $validated = $request->validate([
        'act_check' => 'required|integer',
        'check_date' => 'required|date', 
    ]);

    $registration->act_check = $validated['act_check'];
    $registration->check_date = $validated['check_date']; 
    $registration->save();

}
    
// app/Http/Controllers/ActivityRegisterController.php
public function edit(ActivityRegister $registration): Response
{
    // $this->authorize('update', $registration);
    $activities = ActivityInformation::all();

    return Inertia::render('Admin/Activities/Registrations/ActivtyDetail', [
        'activity' => $registration,  // ใช้ 'activity' แทน 'registration'
        'activities' => $activities,
        'currentUser' => Auth::user(),
    ]);
}

public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required',
        'fname' => 'required',
        'lname' => 'required',
        'activity_name' => 'required',
        'date' => 'required|date',
        'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'description' => 'nullable|string',
        'institution' => 'nullable|string',
        'launch' => 'nullable|date',
        'end' => 'nullable|date',
        'location' => 'nullable|string',
        'hours' => 'nullable|numeric',
    ]);

    $registration = ActivityRegister::findOrFail($id);

    $registration->title = $request->input('title');
    $registration->fname = $request->input('fname');
    $registration->lname = $request->input('lname');
    $registration->activity_name = $request->input('activity_name');
    $registration->date = $request->input('date');
    $registration->description = $request->input('description');
    $registration->institution = $request->input('institution');
    $registration->launch = $request->input('launch');
    $registration->end = $request->input('end');
    $registration->location = $request->input('location');
    $registration->hours = $request->input('hours');

    if ($request->hasFile('file')) {
        if ($registration->file) {
            Storage::disk('public')->delete($registration->file);
        }
        $file = $request->file('file');
        $filePath = $file->store('activity', 'public');
        $registration->file = $filePath;
    }

    $registration->save();

    return redirect()->route('activity_registrations.index')->with('success', 'Activity updated successfully.');
}

}
