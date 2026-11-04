<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles administrator management of contact form submissions.
 */
class ContactController
{
    /**
     * Display a listing of contact messages with search & filtering.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = ContactMessage::query()->latest();

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->withQueryString();
        $unreadCount = ContactMessage::unread()->count();
        $totalCount = ContactMessage::count();

        return view('admin.contacts.index', compact('messages', 'unreadCount', 'totalCount', 'status', 'search'));
    }

    /**
     * Display the specified contact message and mark as read.
     */
    public function show(ContactMessage $contact): View
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Mark a contact message as read/unread.
     */
    public function markRead(ContactMessage $contact): RedirectResponse
    {
        $contact->update(['is_read' => !$contact->is_read]);

        return back()->with('success', 'Contact message status updated.');
    }

    /**
     * Delete the specified contact message.
     */
    public function destroy(ContactMessage $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact message deleted successfully.');
    }
}

