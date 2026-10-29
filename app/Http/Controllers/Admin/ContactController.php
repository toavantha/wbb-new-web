<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * List all citizen inquiries.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $query = ContactMessage::query();

        if (!empty($status) && $status !== 'All') {
            $query->where('status', $status);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.contacts.index', compact('messages', 'status'));
    }

    /**
     * Show single contact message.
     */
    public function show(int $id): View
    {
        $message = ContactMessage::findOrFail($id);
        return view('admin.contacts.show', compact('message'));
    }

    /**
     * Update status and notes on inquiry.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:New,In Progress,Responded,Resolved',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $message->update($validated);

        return redirect()->route('admin.contacts.show', $message->id)
            ->with('success', 'Message status updated.');
    }

    /**
     * Delete contact message.
     */
    public function destroy(int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Inquiry deleted successfully.');
    }
}
