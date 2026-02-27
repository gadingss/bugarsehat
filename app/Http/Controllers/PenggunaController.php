<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repository\MenuRepository;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar pengguna (Owner & Staff).
     */
    public function index(Request $request)
    {
        $config = [
            'title' => 'Pengguna',
            'title-alias' => 'Manajemen Pengguna',
            'menu' => MenuRepository::generate($request),
        ];

        $pengguna = User::whereIn('role', ['Owner', 'Staff'])
            ->orderBy('id', 'asc')
            ->get();

        // FIX: Menggunakan view 'pengguna.index'
        return view('pengguna.index', compact('config', 'pengguna'));
    }

    /**
     * Menampilkan daftar Member saja.
     */
    public function daftarMember(Request $request)
    {
        $config = [
            'title' => 'Daftar Member',
            'title-alias' => 'Membership',
            'menu' => MenuRepository::generate($request),
        ];

        $members = User::where('role', 'Member')
            ->orderBy('name', 'asc')
            ->get();

        return view('member.index', compact('config', 'members'));
    }

    public function storeMember(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'password' => 'required|string|min:8|confirmed',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => 'Member',
    ]);

    return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan!');
}

public function updateMember(Request $request, $id)
{
    $member = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required','email', Rule::unique('users')->ignore($member->id)],
        'phone' => 'nullable|string|max:20',
    ]);

    $member->update($request->only(['name','email','phone']));

    return redirect()->route('member.index')->with('success', 'Member berhasil diperbarui!');
}

public function destroyMember($id)
{
    $member = User::findOrFail($id);
    $member->delete();

    return redirect()->route('member.index')->with('success', 'Member berhasil dihapus!');
}


    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create(Request $request)
    {
        $config = [
            'title' => 'Tambah Pengguna',
            'title-alias' => 'Form Pengguna',
            'menu' => MenuRepository::generate($request),
        ];

        return view('pengguna.create', compact('config'));
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:User:Owner,User:Staff,User:Trainer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Map to existing enum values: keep 'owner' for owner, otherwise use 'staff' for staff/trainer
        $enumRole = $request->role === 'User:Owner' ? 'owner' : 'staff';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $enumRole,
        ]);

        // Assign Spatie role
        $user->assignRole($request->role);

        // FIX: Redirect ke 'pengguna.index'
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(Request $request, User $pengguna)
    {
        $config = [
            'title' => 'Edit Pengguna',
            'title-alias' => 'Form Edit Pengguna',
            'menu' => MenuRepository::generate($request),
        ];

        return view('pengguna.edit', compact('config', 'pengguna'));
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($pengguna->id),
            ],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:User:Owner,User:Staff,User:Trainer',
        ]);

        $pengguna->update($request->only(['name', 'email', 'phone']));

        // Sync Spatie role and keep enum column for compatibility
        $pengguna->syncRoles([$request->role]);
        $pengguna->role = $request->role === 'User:Owner' ? 'owner' : 'staff';
        $pengguna->save();

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $pengguna->update(['password' => Hash::make($request->password)]);
        }

        // FIX: Redirect ke 'pengguna.index'
        return redirect()->route('pengguna')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $pengguna)
    {
        $pengguna->delete();

        // FIX: Redirect ke 'pengguna.index'
        return redirect()->route('pengguna')->with('success', 'Pengguna berhasil dihapus!');
    }
}
