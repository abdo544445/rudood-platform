<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::latest();

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('subject', 'like', $search)
                  ->orWhere('message', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $messages = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => ContactMessage::count(),
            'new'         => ContactMessage::where('status', 'new')->count(),
            'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
            'resolved'    => ContactMessage::where('status', 'resolved')->count(),
        ];

        return view('admin.contact-messages.index', compact('messages', 'stats'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status'      => 'required|in:new,in_progress,resolved',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $message = ContactMessage::findOrFail($id);
        $oldStatus = $message->status;

        $message->update([
            'status'      => $request->status,
            'admin_notes' => $request->filled('admin_notes') ? $request->admin_notes : $message->admin_notes,
        ]);

        AuditLog::record(
            'contact.status_update',
            "تم تعديل حالة رسالة التواصل #{$message->id} من [{$oldStatus}] إلى [{$request->status}]",
            'admin',
            ['contact_id' => $message->id, 'new_status' => $request->status]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $message->status,
                'label'   => $message->status_label,
                'badge'   => $message->status_badge_class,
            ]);
        }

        return back()->with('status', 'تم تحديث حالة الرسالة بنجاح ✓');
    }

    public function destroy(int $id)
    {
        $message = ContactMessage::findOrFail($id);
        $senderName = $message->name;
        $message->delete();

        AuditLog::record(
            'contact.deleted',
            "تم حذف رسالة التواصل #{$id} المستلمة من ({$senderName})",
            'admin',
            ['contact_id' => $id]
        );

        return back()->with('status', 'تم حذف الرسالة بنجاح ✓');
    }
}
