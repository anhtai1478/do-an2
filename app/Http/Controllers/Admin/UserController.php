<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function profile(): View
    {
        return view('admin.user.profile', [
            'user' => auth()->user(),
            'countries' => Country::all(),
        ]);
    }

    public function index(Request $request): View
    {
        $query = User::with('country')->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level') && in_array($request->input('level'), ['0', '1', '2'], true)) {
            $query->where('level', (int) $request->input('level'));
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'countries'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'id_country' => 'nullable|exists:countries,id',
            'level' => 'required|in:0,1,2',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($user->id === auth()->id() && (int) $validated['level'] !== 2) {
            return back()->withErrors(['level' => 'Bạn không thể tự hạ quyền Admin của chính mình.']);
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'id_country' => $validated['id_country'] ?? null,
            'level' => (int) $validated['level'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;
            $data['avatar'] = $request->file('avatar')->store('upload/user/avatar', 'public');

            if ($oldAvatar && str_contains($oldAvatar, '/') && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin thành viên thành công.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Đã xóa thành viên thành công.');
    }
}
