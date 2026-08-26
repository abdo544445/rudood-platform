<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Workspace;

class AdminAuditLogController extends Controller
{
    /**
     * Display a listing of system audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['workspace', 'user'])->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('workspace_id')) {
            $query->where('workspace_id', $request->workspace_id);
        }

        $logs = $query->paginate(25)->withQueryString();
        $workspaces = Workspace::orderBy('company_name')->get();

        return view('admin.audit-logs.index', compact('logs', 'workspaces'));
    }
}
