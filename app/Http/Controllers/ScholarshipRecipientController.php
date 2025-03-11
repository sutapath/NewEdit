<?php
namespace App\Http\Controllers;

use App\Models\ScholarshipRecipient;
use Illuminate\Http\Request;

class ScholarshipRecipientController extends Controller
{
    public function index()
    {
        $recipients = ScholarshipRecipient::all();
        return view('recipients.index', compact('recipients'));
    }

    public function create()
    {
        return view('recipients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'apply_id' => 'required|exists:scholarship_applications,id',
        ]);

        ScholarshipRecipient::create($request->all());

        return redirect()->route('recipients.index')
                         ->with('success', 'Recipient created successfully.');
    }

    public function show(ScholarshipRecipient $recipient)
    {
        return view('recipients.show', compact('recipient'));
    }

    public function edit(ScholarshipRecipient $recipient)
    {
        return view('recipients.edit', compact('recipient'));
    }

    public function update(Request $request, ScholarshipRecipient $recipient)
    {
        $request->validate([
            'apply_id' => 'required|exists:scholarship_applications,id',
        ]);

        $recipient->update($request->all());

        return redirect()->route('recipients.index')
                         ->with('success', 'Recipient updated successfully.');
    }

    public function destroy(ScholarshipRecipient $recipient)
    {
        $recipient->delete();

        return redirect()->route('recipients.index')
                         ->with('success', 'Recipient deleted successfully.');
    }
}
