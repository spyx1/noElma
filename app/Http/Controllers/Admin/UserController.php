<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Controllers\ReferenceTableController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function selectionIds(Request $request): JsonResponse
    {
        $query = User::query();
        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('middle_name', 'like', "%{$search}%");
            });
        }
        return response()->json(['ids' => $query->pluck('id')]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function show(User $user): View
    {
        $returnTo = request()->string('return_to')->value();
        if (!str_starts_with($returnTo, url('/'))) {
            $returnTo = route('workspace.users.index');
        }

        if (parse_url($returnTo, PHP_URL_PATH) === '/tables') {
            return view('tables.index', [...app(ReferenceTableController::class)->indexData(), 'modalUser' => $user, 'modalReturnTo' => $returnTo]);
        }

        return view('admin.users.show', compact('user', 'returnTo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_mode' => ['required', Rule::in(['initials', 'gravatar', 'upload'])],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(array_keys(User::roles()))],
        ]);

        User::create([
            'name' => trim("{$data['last_name']} {$data['first_name']} {$data['middle_name']}"),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'],
            'email' => $data['email'],
            'phone' => $data['phone'], 'avatar_mode' => $data['avatar_mode'], 'avatar_path' => $request->file('avatar')?->store('avatars', 'public'),
            'password' => $data['password'],
            'role' => $data['role'],
            'is_admin' => $data['role'] === User::ROLE_ADMIN,
        ]);

        return redirect()->route('workspace.users.index')->with('status', 'Пользователь создан.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_mode' => ['required', Rule::in(['initials', 'gravatar', 'upload'])],
            'role' => ['required', Rule::in(array_keys(User::roles()))],
            'password' => ['nullable', 'min:8'],
            'password_confirmation' => ['nullable', 'required_with:password', 'same:password'],
        ]);

        $user->fill([
            'name' => trim("{$data['last_name']} {$data['first_name']} {$data['middle_name']}"),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'avatar_mode' => $data['avatar_mode'],
            'role' => $data['role'],
            'is_admin' => $data['role'] === User::ROLE_ADMIN,
        ]);

        if ($request->hasFile('avatar')) $user->avatar_path = $request->file('avatar')->store('avatars', 'public');

        if ($data['password'] ?? null) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('workspace.users.index')->with('status', 'Пользователь обновлён.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'Нельзя удалить свою учётную запись.']);
        }

        $user->delete();

        return redirect()->route('workspace.users.index')->with('status', 'Пользователь удалён.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer', 'exists:users,id']])['ids'];
        $deleted = User::query()->whereIn('id', $ids)->where('id', '!=', $request->user()->id)->delete();

        return redirect()->route('workspace.users.index')->with('status', "Удалено пользователей: {$deleted}.");
    }
}
