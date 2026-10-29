<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserRegistrationController extends Controller
{
    public function index(): View
    {
        $pendingUsers = User::query()
            ->where('is_approved', false)
            ->whereNotNull('email_verified_at')
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('pendingUsers'));
    }

    public function currentUsers(): View
    {
        $currentUsers = User::query()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.users.current', compact('currentUsers'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'editor'])],
            'is_approved' => ['required', Rule::in(['0', '1'])],
        ]);

        $isApproved = (bool) $validated['is_approved'];
        $isChangingOwnAccess = $request->user()->is($user)
            && ($validated['role'] !== $user->role || ! $isApproved);
        $isApprovingUnverifiedUser = ! $user->is_approved
            && $isApproved
            && ! $user->hasVerifiedEmail();
        $isRemovingLastApprovedAdmin = $user->role === 'admin'
            && $user->is_approved
            && ($validated['role'] !== 'admin' || ! $isApproved)
            && User::query()->where('role', 'admin')->where('is_approved', true)->count() === 1;

        abort_if($isChangingOwnAccess || $isApprovingUnverifiedUser || $isRemovingLastApprovedAdmin, 403);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_approved' => $isApproved,
        ]);

        return redirect()->route('admin.users.current')->with('success', 'CMS user details updated.');
    }

    public function setTemporaryPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'temporary_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => $validated['temporary_password'],
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Temporary password set. The user must change it the next time they access the CMS.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 403);
        abort_if(
            $user->role === 'admin'
                && $user->is_approved
                && User::query()->where('role', 'admin')->where('is_approved', true)->count() === 1,
            403,
        );

        $user->delete();

        return redirect()->route('admin.users.current')->with('success', 'CMS user deleted.');
    }

    public function approve(User $user): RedirectResponse
    {
        abort_unless(! $user->is_approved && $user->hasVerifiedEmail(), 404);

        $user->update(['is_approved' => true]);

        return redirect()->route('admin.users.index')->with('success', 'User registration approved.');
    }

    public function reject(User $user): RedirectResponse
    {
        abort_unless(! $user->is_approved, 404);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User registration rejected.');
    }
}
