<?php

namespace App\Http\Controllers;

use App\Models\ActivityInformation;
use App\Models\ActivitySave;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\UserLineNotify;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ActivityInformationController extends Controller
{
    public function index(): Response
    {
        $activities = ActivityInformation::with('user')->get(); 
        $activitysave = ActivitySave::all(); 
        $currentUser = Auth::user(); // ดึงข้อมูลผู้ใช้งานปัจจุบัน
    
        return Inertia::render('Admin/Activities/Index', [
            'activities' =>  $activities,
            'activitysave' =>  $activitysave,
            'currentUser' => $currentUser, // ส่งข้อมูลผู้ใช้งานปัจจุบันไปยัง frontend
        ]);
    }
    

    public function register(RegisterForActivityRequest $request, $activityId)
    {
        $activity = ActivityInformation::findOrFail($activityId);

        if (!$activity->hasAvailableSlots()) {
            return redirect()->back()->with('error', 'ไม่สามารถลงทะเบียนได้เนื่องจากจำนวนที่ลงทะเบียนเต็ม');
        }

        $registration = new ActivityRegister($request->all());
        $registration->activity_id = $activityId;
        $registration->save();

        return redirect()->route('activities.show', $activityId)->with('success', 'ลงทะเบียนเรียบร้อย');
    }

    public function create(): Response
    {
        // ดึงข้อมูลผู้ใช้ทั้งหมด
        $users = User::all();
        $userLineNotify = [];
    
        // วนลูปผ่านผู้ใช้แต่ละคน
        foreach ($users as $user) {
            // ตรวจสอบบทบาทของผู้ใช้
            if ($user->hasRole('admin') || $user->hasRole('officer') || $user->hasRole('scholar')) {
                // ดึงข้อมูล LINE Notify ของผู้ใช้ที่มี token
                $userLineNotify[] = UserLineNotify::where('user_id', $user->id)
                    ->whereNotNull('line_notify_token')
                    ->get()
                    ->toArray();
            }
        }
    
        // ส่งข้อมูลไปยัง Inertia view
        return Inertia::render('Admin/Activities/Create', [
            'userLineNotify' => array_merge([], ...$userLineNotify), // รวมข้อมูลทั้งหมดในอาเรย์เดียว
        ]);
    }
    
    
    
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            // 'end_date' => 'required|date',
            'location' => 'required|string',
            'organizer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'limits' => 'required|integer',
            'regis_due' => 'required|date',
            'link' => 'nullable|string',
            'hours' => 'nullable|integer',
            'user_id' => 'exists:users,id',
            // 'hours' => 'required|integer',
        ]);


        ActivityInformation::create($request->all());

        return redirect()->route('activities.index');

    }

    public function edit(ActivityInformation $activity): Response
    {
        // $this->authorize('update', $activity);

        return Inertia::render('Admin/Activities/Edit', [
            'activity' => $activity,
        ]);
    }

    public function update(Request $request, ActivityInformation $activity): RedirectResponse
    {
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            // 'end_date' => 'required|date',
            'location' => 'required|string',
            'organizer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'limits' => 'required|integer',
            'regis_due' => 'required|date',
            'link' => 'nullable|string',
            // 'hours' => 'required|integer',
        ]);

        // $this->authorize('update', $activity);

        $activity->update($request->all());

        return to_route('activities.index');
    }

public function destroy(ActivityInformation $activity): RedirectResponse
{
    // ลบกิจกรรม
    if ($activity) {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'กิจกรรมถูกลบเรียบร้อยแล้ว.');
    }

    return redirect()->route('activities.index')->with('error', 'ไม่พบกิจกรรมที่ต้องการลบ.');
}

}