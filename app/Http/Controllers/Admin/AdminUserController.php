<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function index()
    {
        $users = User::withCount(['transactions', 'debts'])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(CreateUserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        $this->activityLogService->log(
            'CREATE_USER',
            "Admin membuat user baru: {$user->name} ({$user->email}) dengan role {$user->role}",
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} berhasil dibuat.");
    }

    public function edit(int $id)
    {
        $user = User::withCount(['transactions', 'debts'])->findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = [
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $this->activityLogService->log(
            'UPDATE_USER',
            "Admin mengupdate data user ID:{$id} ({$user->name}). Fields: email, role" . ($request->filled('password') ? ', password' : ''),
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }
}
