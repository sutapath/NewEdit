<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Resources\ScholarshipsResource;
use App\Http\Requests\CreateScholarshipsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLineNotify;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class ScholarshipController extends Controller
{
    public function index(): Response
    {
        $scholarships = Scholarship::with('user')->get();

        $user = $scholarships->first()->user ?? null;

        return Inertia::render('Admin/Scholarships/Index', [
            'scholarships' => ScholarshipsResource::collection($scholarships),
            'user' => $user
        ]);
    }
    public function Landing(): Response
    {
        $scholarships = Scholarship::all(); // ดึงข้อมูลจากฐานข้อมูล
        return Inertia::render('Welcome', [
            'scholarships' => ScholarshipsResource::collection($scholarships),
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
    public function dashBoard(): Response
    {
        $scholarships = Scholarship::all(); // ดึงข้อมูลจากฐานข้อมูล
        return Inertia::render('Dashboard', [
            'scholarships' => ScholarshipsResource::collection($scholarships),
        ]);
    }
    public function create(): Response
    {
        $users = User::role(['admin', 'officer', 'student', 'member'])->pluck('id');
        $userLineNotify = UserLineNotify::whereIn('user_id', $users)
            ->whereNotNull('line_notify_token')
            ->get(['user_id', 'line_notify_token']);
        return Inertia::render('Admin/Scholarships/Create', [
            'userLineNotify' => $userLineNotify,
        ]);
    }
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'scholar_name' => 'sometimes|string|max:255',
            'type' => 'sometimes|integer',
            'apply_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'status' => 'sometimes|integer',
            'limits' => 'sometimes|integer',
            'description' => 'sometimes|string',
            'link' => 'nullable|string|url',
            'officer_code' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5012',
        ]);
        if ($request->hasFile('file')) {
            $fileName = time() . '_file.' . $request->file('file')->extension();
            $request->file('file')->move(public_path('storage/files'), $fileName);
            $validatedData['file'] = $fileName;
        }
        Scholarship::create($validatedData);

        return to_route('scholarships.index');
    }
    public function edit($id): Response
    {
        $scholarship = Scholarship::findOrFail($id);
        return Inertia::render('Admin/Scholarships/Edit', [
            'scholarship' => $scholarship,
        ]);
    }
    public function update(Request $request, Scholarship $scholarship): RedirectResponse
    {
        $validatedData = $request->validate([
            'scholar_name' => 'sometimes|string|max:255',
            'type' => 'sometimes|integer',
            'apply_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'status' => 'sometimes|integer',
            'limits' => 'sometimes|integer',
            'description' => 'sometimes|string',
            'link' => 'nullable|string|url',
            'officer_code' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048',
        ]);
        if ($request->hasFile('file')) {
            if ($scholarship->file && file_exists(public_path('storage/files/' . $scholarship->file))) {
                unlink(public_path('storage/files/' . $scholarship->file));
            }
            $fileName = time() . '_file.' . $request->file('file')->extension();
            $request->file('file')->move(public_path('storage/files'), $fileName);
            $validatedData['file'] = $fileName;
        }
        $scholarship->update($validatedData);

        return to_route('scholarships.index')->with('success', 'Scholarship updated successfully.');
    }
    public function show(Scholarship $scholarship): Response
    {
        $scholarship = $scholarship->load('user');
        $user = $scholarship->user;
        return Inertia::render('Admin/Scholarships/Show', [
            'scholarship' => $scholarship,
            'user' => $user
        ]);
    }
    public function destroy(Scholarship $scholarship): RedirectResponse
    {
        $scholarship->delete();
        return back();
    }
}
