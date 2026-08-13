<?php
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Departemen;
use App\Models\Plant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua user sesuai UUID plant user login
    public function index(Request $request)
    {
        $userPlantUuid = auth()->user()->plant;

        $query = User::with(['plantRelasi', 'departmentRelasi'])
            ->where('plant', $userPlantUuid);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('plantRelasi', function ($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%");
            })
            ->orWhereHas('departmentRelasi', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10);
        $users->appends($request->all());

        $plants = Plant::get();

        return view('user.index', compact('users', 'plants'));
    }


    //save multiplant options dari tabel user
    public function updatePlantOption(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $user->plant_option = $request->filled('plant_option')
            ? $request->plant_option
            : null;

        $user->save();

        return back()->with('success', 'Akses multiplant berhasil diperbarui.');
    }


    //merubah plant session 
    public function changePlant(Request $request)
    {
        $user = auth()->user();

        $plantOption = json_decode(
            \Illuminate\Support\Facades\DB::table('users')
                ->where('uuid', $user->uuid)
                ->value('plant_option'),
            true
        ) ?? [];

        if (!in_array($request->plant_active, $plantOption)) {
            return response()->json([
                'success' => false,
                'message' => 'Plant tidak tersedia.'
            ], 403);
        }

        \Illuminate\Support\Facades\DB::table('users')
            ->where('uuid', $user->uuid)
            ->update([
                'plant_active' => $request->plant_active,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function create()
    {
        $plants = Plant::select('uuid', 'plant')->get(); // ambil UUID plant
        $departments = Departemen::select('id', 'nama')->get();

        return view('user.create', compact('plants', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email|unique:users,email',
            'plant' => 'nullable|string', // ini akan menampung UUID
            'department' => 'nullable|string',
            'type_user' => 'required|integer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'plant' => $request->plant, // UUID plant
            'department' => $request->department,
            'type_user' => $request->type_user,
            'updater' => auth()->user()->name,
        ]);

        // mapping type_user -> role Spatie
        $roleName = User::TYPE_USER_ROLE_MAP[$request->type_user] ?? null;

        if ($roleName) {
            // hapus role lama (kalau ada) dan set role baru sesuai type_user
            $user->syncRoles([$roleName]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil dibuat');
    }


    public function edit(User $user)
    {
        $plants = Plant::select('uuid', 'plant')->get();
        $departments = Departemen::all();
        return view('user.edit', compact('user', 'plants', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->uuid . ',uuid',
            'password' => 'nullable|string|min:6',
            'email' => 'nullable|email|unique:users,email,' . $user->uuid . ',uuid',
            'plant' => 'nullable|string', // UUID plant
            'department' => 'nullable|string',
            'type_user' => 'required|integer',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'email' => $request->email,
            'plant' => $request->plant, // UUID plant
            'department' => $request->department,
            'type_user' => $request->type_user,
            'updater' => auth()->user()->name,
        ]);

        // mapping type_user -> role Spatie
        $roleName = User::TYPE_USER_ROLE_MAP[$request->type_user] ?? null;

        if ($roleName) {
            $user->syncRoles([$roleName]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }
}
