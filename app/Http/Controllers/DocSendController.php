<?php

namespace App\Http\Controllers;

use App\Models\DocSend;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentRequest;


class DocSendController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            $docsSends = DocSend::all();
            $publicInfo = DocumentRequest::where('type', 1)->orderBy('created_at', 'desc')->get();
        } else {
            $publicInfo = DocumentRequest::where('type', 1)->orderBy('created_at', 'desc')->get();

            $docsSends = DocSend::where('user_id', auth()->user()->id)->get();
        }
        return Inertia::render('Admin/DocsSends/Index', [
            'docsSends' => $docsSends,
            'publicInfos' => $publicInfo,

        ]);
    }
    public function create()
    {
        return Inertia::render('Admin/DocsSends/Create');
    }
    public function store(Request $request)
    {
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
            $fileName = time() . '_operat_docs.' . $request->file('operat_docs')->extension();
            $request->file('operat_docs')->move(public_path('storage/files'), $fileName);
            $validatedData['operat_docs'] = $fileName;
        }
        if ($request->hasFile('transcripts')) {
            $fileName = time() . '_transcripts.' . $request->file('transcripts')->extension();
            $request->file('transcripts')->move(public_path('storage/files'), $fileName);
            $validatedData['transcripts'] = $fileName;
        }
        DocSend::create($validatedData);
        return redirect()->route('docs_sends.index')->with('success', 'Document sent successfully.');
    }
    public function show($id)
    {
        $docSend = DocSend::findOrFail($id);
        $user = auth()->user();
        return Inertia::render('Admin/DocsSends/Show', [
            'docSend' => $docSend,
            'user' => $user
        ]);
    }
    public function edit($id)
    {
        $docSend = DocSend::findOrFail($id);
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
        if ($request->hasFile('operat_docs')) {
            if ($docSend->operat_docs && file_exists(public_path('storage/files/' . $docSend->operat_docs))) {
                unlink(public_path('storage/files/' . $docSend->operat_docs));
            }
            $fileName = time() . '_operat_docs.' . $request->file('operat_docs')->extension();
            $request->file('operat_docs')->move(public_path('storage/files'), $fileName);
            $validatedData['operat_docs'] = $fileName;
        }
        if ($request->hasFile('transcripts')) {
            if ($docSend->transcripts && file_exists(public_path('storage/files/' . $docSend->transcripts))) {
                unlink(public_path('storage/files/' . $docSend->transcripts));
            }
            $fileName = time() . '_transcripts.' . $request->file('transcripts')->extension();
            $request->file('transcripts')->move(public_path('storage/files'), $fileName);
            $validatedData['transcripts'] = $fileName;
        }
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
        $docSend = DocSend::findOrFail($id); 
        $docSend->delete();
        return back();
    }
}
