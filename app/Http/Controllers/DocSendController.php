<?php

namespace App\Http\Controllers;

use App\Models\DocSend;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class DocSendController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        // ตรวจสอบบทบาทของผู้ใช้
        if (auth()->user()->hasRole('admin')) {
            // ถ้าเป็น Admin ให้ดึงข้อมูลทั้งหมด
            $docsSends = DocSend::all();
        } else {
            // ถ้าเป็นผู้ใช้ทั่วไป ให้ดึงข้อมูลที่เกี่ยวข้องกับผู้ใช้ที่ล็อกอิน
            $docsSends = DocSend::where('user_id', auth()->user()->id)->get();
        }
    
        // ส่งข้อมูลไปยัง Inertia
        return Inertia::render('Admin/DocsSends/Index', [
            'docsSends' => $docsSends,
        ]);
    }
    
    // Show the form for creating a new resource.
    public function create()
    {
        // $this->authorize('create', DocSend::class); // ตรวจสอบสิทธิ์การสร้างเอกสาร

        return Inertia::render('Admin/DocsSends/Create');
    }
    public function store(Request $request)
    {
        // $this->authorize('create', DocSend::class); // Check authorization
        
        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'title' => 'string',
            'fname' => 'string',
            'lname' => 'string',
            'operat_docs' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'transcripts' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'date' => 'date',
            'years' => 'string',
            'suggestions' => 'string',
            'result' => 'string|max:5',
        ]);
        if ($request->hasFile('operat_docs')) {
            $fileName = time().'_operat_docs.'.$request->file('operat_docs')->extension();
            $request->file('operat_docs')->move(public_path('storage/files'), $fileName);
            $validatedData['operat_docs'] = $fileName;
        }
    
        if ($request->hasFile('transcripts')) {
            $fileName = time().'_transcripts.'.$request->file('transcripts')->extension();
            $request->file('transcripts')->move(public_path('storage/files'), $fileName);
            $validatedData['transcripts'] = $fileName;
        }
        DocSend::create($validatedData);
    
        return redirect()->route('docs_sends.index')->with('success', 'Document sent successfully.');
    }
     
    public function show($id)
    {
        $docSend = DocSend::findOrFail($id);
        $user = auth()->user(); // Get the authenticated user
    
        return Inertia::render('Admin/DocsSends/Show', [
            'docSend' => $docSend,
            'user' => $user
        ]);
    }
    public function edit($id)
    {
        // ใช้ findOrFail เพื่อค้นหา DocSend ตาม ID
        $docSend = DocSend::findOrFail($id); 
        
        // ส่งข้อมูลไปยัง Inertia
        return Inertia::render('Admin/DocsSends/Edit', [
            'docSend' => $docSend, 
        ]);
    }
    
    public function update(Request $request, DocSend $docSend)
    { 
            $validatedData = $request->validate([
                'title' => 'required|string',
                'fname' => 'required|string',
                'lname' => 'required|string',
                'date' => 'required|date',
                'years' => 'required|string',
                'operat_docs' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'transcripts' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            ]);
    
            // Check and save 'operat_docs' file
            if ($request->hasFile('operat_docs')) {
                // Delete the old file if it exists
                if ($docSend->operat_docs && file_exists(public_path('storage/files/' . $docSend->operat_docs))) {
                    unlink(public_path('storage/files/' . $docSend->operat_docs));
                }
    
                // Save the new file
                $fileName = time().'_operat_docs.'.$request->file('operat_docs')->extension();
                $request->file('operat_docs')->move(public_path('storage/files'), $fileName);
                $validatedData['operat_docs'] = $fileName;
            }
    
            // Check and save 'transcripts' file
            if ($request->hasFile('transcripts')) {
                // Delete the old file if it exists
                if ($docSend->transcripts && file_exists(public_path('storage/files/' . $docSend->transcripts))) {
                    unlink(public_path('storage/files/' . $docSend->transcripts));
                }
    
                // Save the new file
                $fileName = time().'_transcripts.'.$request->file('transcripts')->extension();
                $request->file('transcripts')->move(public_path('storage/files'), $fileName);
                $validatedData['transcripts'] = $fileName;
            }
    
            // Update the record in the database
            $docSend->update($validatedData);
    
            return redirect()->route('docs_sends.index')->with('status', 'Document updated successfully.');
        }  
  
    

    

    public function check(Request $request, DocSend $docSend)
    {
     
        $validatedData = $request->validate([
            'result' => 'required|string|max:5',
            'suggestions' => 'nullable|string',
            'check_date' => 'nullable|date',
        ]);
    
        $docSend->update($validatedData);
    
        return redirect()->route('docs_sends.index')->with('status', 'Document checked successfully.');
    }
    
    

    public function destroy($id): RedirectResponse
    {
        $docSend = DocSend::findOrFail($id); // ค้นหาด้วย ID หรือแสดงข้อผิดพลาด 404 หากไม่พบ
        // $this->authorize('delete', $docSend); // ตรวจสอบสิทธิ์การลบเอกสาร
        $docSend->delete();
        return back();
    }
    
}
