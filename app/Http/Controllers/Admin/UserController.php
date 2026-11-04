<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Handles user management in the admin panel with real database records.
 */
class UserController
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $roleFilter = trim((string) $request->input('role', ''));

        $query = User::query()
            ->withCount(['bookmarks', 'discussions', 'reports']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalUsers = User::count();
        $adminCount = User::where('role', UserRole::Admin->value)->count();
        $studentCount = User::where('role', UserRole::User->value)->count();

        return view('admin.users.index', compact(
            'users',
            'search',
            'roleFilter',
            'totalUsers',
            'adminCount',
            'studentCount'
        ));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->loadCount(['bookmarks', 'discussions', 'reports', 'blogs']);

        $recentDiscussions = $user->discussions()
            ->with('question')
            ->latest('id')
            ->take(5)
            ->get();

        $recentReports = $user->reports()
            ->with('question')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.users.show', compact('user', 'recentDiscussions', 'recentReports'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' was successfully updated.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user() && $request->user()->id === $user->id) {
            return back()->with('error', 'You cannot delete your own administrative account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' was successfully removed.");
    }
}
