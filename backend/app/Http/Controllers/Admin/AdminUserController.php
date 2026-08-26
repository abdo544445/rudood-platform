<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users and store owners.
     */
    public function index(Request $request)
    {
        $query = User::with('workspace');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('workspace', fn($w) => $w->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('workspace_id')) {
            $query->where('workspace_id', $request->workspace_id);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $workspaces = Workspace::orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.users.index', compact('users', 'workspaces'));
    }

    /**
     * Update user details (name, email, phone, role, workspace).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'        => 'nullable|string|max:50',
            'role'         => 'required|string|in:owner,agent,admin',
            'workspace_id' => 'required|exists:workspaces,id',
        ]);

        $user->update($validated);

        return back()->with('success', "تم تحديث بيانات المستخدم ({$user->name}) بنجاح ✓");
    }

    /**
     * Directly reset user password from the Super Admin panel.
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "تم تغيير كلمة المرور للمستخدم ({$user->name}) بنجاح ✓");
    }

    /**
     * Delete a user account safely.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الشخصي الذي قمت بتسجيل الدخول منه.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "تم حذف المستخدم ({$userName}) بنجاح.");
    }
}
