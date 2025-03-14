<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\ScholarshipApplication;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\UserLineNotify;
use App\Http\Resources\ScholarshipsResource;

class PublishRequestController extends Controller
{
    // public function createInfo(): Response
    // {
    //     $scholarships = Scholarship::all(); // ดึงข้อมูลทุนทั้งหมด
    //     $scholarshipApplications = ScholarshipApplication::all(); // ดึงข้อมูลใบสมัครทั้งหมด
    //     $userLineNotify = UserLineNotify::all(); // ดึงข้อมูล Line Notify

    //     // ส่งข้อมูลไปยัง Vue component ผ่าน Inertia
    //     return Inertia::render('Admin/Scholarships/ApplyScholars/PublishInfo', [
    //         'scholar' => $scholarships, // ส่งข้อมูลทุนทั้งหมดไปยัง Vue
    //         'scholarshipApplications' => $scholarshipApplications,
    //         'userLineNotify' => $userLineNotify,
    //     ]);
    // }



    public function createInfo(Request $request): Response
    {
        $scholar_id = $request->input('scholar_id');
        $scholarships = Scholarship::all();
        $approvedApplications = ScholarshipApplication::where('Interview_results', '1')
            ->where('scholar_id', $scholar_id)
            ->get();
        $userIds = $approvedApplications->pluck('user_id')->map(fn($id) => (int) $id)->toArray();
        $userLineNotify = UserLineNotify::whereIn('user_id', $userIds)
            ->whereNotNull('line_notify_token')
            ->get()
            ->toArray(); 
        return Inertia::render('Admin/Scholarships/ApplyScholars/PublishInfo', [
            'scholarships' => $scholarships,
            'approvedApplications' => $approvedApplications,
            'userLineNotify' => $userLineNotify,
        ])->with('success', 'Document request created successfully.');
    }
 
    public function index(): Response
    {
        $documentRequests = DocumentRequest::with('user')->get();

        return Inertia::render('Admin/DocsSends/Index', [
            'documentRequests' => $documentRequests,
        ]);
    }
    public function create(): Response
    {
        $users = User::all();
        $userLineNotify = [];
        foreach ($users as $user) {
            if ($user->hasRole('admin') || $user->hasRole('intlscholar') || $user->hasRole('officer') || $user->hasRole('scholar')) {
                $userLineNotify[] = UserLineNotify::where('user_id', $user->id)
                    ->whereNotNull('line_notify_token')
                    ->get()
                    ->toArray();
            }
        }

        return Inertia::render('Admin/DocsSends/LineNotifyforRequireDocs', [
            'userLineNotify' => array_merge([], ...$userLineNotify),

        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'document_type' => 'sometimes|string|max:255',
            'send_date' => 'sometimes|date',
            'type' => 'sometimes|integer',
            'scholar_id' => 'sometimes|integer',
            'location' => 'sometimes|String',
        ]); 
        $document = DocumentRequest::where('scholar_id', $request->scholar_id)->first();
        if ($document) { 
            $document->update([
                'document_type' => $request->document_type,
                'send_date' => $request->send_date,
                'type' => $request->type,
                'location' => $request->location,
            ]);
            $message = 'Document request updated successfully.';
        } else { 
            DocumentRequest::create([
                'document_type' => $request->document_type,
                'send_date' => $request->send_date,
                'type' => $request->type,
                'location' => $request->location,
                'scholar_id' => $request->scholar_id,
            ]);
            $message = 'Document request created successfully.';
        } 
        return redirect()->route('docs_sends.index')->with('success', $message);
    }
    public function show($id)
    {
        $publish = DocumentRequest::findOrFail($id);

        return Inertia::render('Admin/Scholarships/ApplyScholars/PublishInfoEdit', [
            'documentRequest' => $publish,
        ]);
    }
    public function EditInfo($id): Response
    {
        $documentRequest = DocumentRequest::find($id);
        $scholar_id = $documentRequest->scholar_id;
        $scholarships = Scholarship::all();
        $approvedApplications = ScholarshipApplication::where('Interview_results', '1')
            ->where('scholar_id', $scholar_id)
            ->get();
        $userIds = $approvedApplications->pluck('user_id')->map(fn($id) => (int) $id)->toArray();
        $userLineNotify = UserLineNotify::whereIn('user_id', $userIds)
            ->whereNotNull('line_notify_token')
            ->get()
            ->toArray();

        // เพิ่ม dd() เพื่อดูข้อมูล
        dd([
            'scholarships' => $scholarships,
            'approvedApplications' => $approvedApplications,
            'userLineNotify' => $userLineNotify,
            'documentRequest' => $documentRequest,
        ]);

        return Inertia::render('Admin/Scholarships/ApplyScholars/PublishInfoEdit', [
            'scholarships' => $scholarships,
            'approvedApplications' => $approvedApplications,
            'userLineNotify' => $userLineNotify,
            'documentRequest' => $documentRequest, // ต้องแน่ใจว่า documentRequest ส่งไป
        ]);
    }



    public function destroy($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);

        $documentRequest->delete();

        return redirect()->back();
    }
}
