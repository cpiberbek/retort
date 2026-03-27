<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ApiController extends Controller
{
    public function syncUser(Request $request)
    {
        $data = $request->json()->all();

        if (empty($data['user'])) {
            return response()->json(['status' => 'error', 'message' => 'User missing'], 400);
        }

        $user = $data['user'];

        if (
            empty($user['username'])
            || empty($user['department']['name'])
            || empty($user['department']['plant'])
        ) {
            return response()->json(['status' => 'error', 'message' => 'Missing required fields'], 400);
        }

        DB::beginTransaction();
        try {
            // Ambil departemen & plant berdasarkan nama
            $departemen = Departemen::firstOrCreate(
                ['nama' => $user['department']['name']],
                ['uuid' => $user['department']['uuid'] ?? Str::uuid()]
            );

            $plant = Plant::firstOrCreate(
                ['plant' => $user['department']['plant']],
                ['uuid' => $user['department']['uuid'] ?? Str::uuid()]
            );

            // Cek user existing
            $existingUser = User::withTrashed()->where('username', $user['username'])->first();

            $userData = [
                'uuid' => $user['uuid'] ?? Str::uuid(),
                'name' => $user['name'] ?? '',
                'username' => $user['username'],
                'email' => $user['email'] ?? null,
                'department' => $departemen->uuid,
                'plant' => $plant->uuid,
                'activation' => $user['activation'] ?? 0,
            ];

            if (!empty($user['password'])) {
                $userData['password'] = $user['password'];
            }

            if ($existingUser) {
                if ($existingUser->trashed()) {
                    $existingUser->restore();
                }

                $existingUser->update($userData);

                if (!empty($user['project_role']['role'])) {
                    // Replace roles instead of adding new ones
                    $existingUser->syncRoles([$user['project_role']['role']]);
                }
            } else {
                $newUser = User::create($userData);

                if (!empty($user['project_role']['role'])) {
                    $newUser->assignRole($user['project_role']['role']);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'User synced successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error('SyncUser Error', ['exception' => $e->getMessage(), 'user' => $user]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function syncPlant(Request $request)
    {
        $data = $request->json()->all();

        if (empty($data['plant'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload: plant missing'], 400);
        }

        $plantData = $data['plant'];

        DB::beginTransaction();
        try {

            $plant = Plant::where('uuid', $plantData['uuid'])
                ->orWhere('plant', 'LIKE', '%' . $plantData['uuid'] . '%')
                ->first();

            if($plant) {
                $plant->update([
                    'uuid' => $plantData['uuid'],
                    'plant' => $plantData['plant']
                ]);
            } else {
                $plant = Plant::create([
                    'uuid' => $plantData['uuid'],
                    'plant' => $plantData['plant']
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Plant & Departemen synced successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error('SyncPlant Error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Desync user dari UUID
     */
    public function desyncUser(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['user_uuid'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload'
                ], 400);
            }

            $user = User::where('uuid', $data['user_uuid'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User desynced successfully: ' . $data['user_uuid']
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aktivasi user
     */
    public function activation(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['user']['uuid'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload: uuid missing'
                ], 400);
            }

            $user = User::where('uuid', $data['user']['uuid'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->activation = 1;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User Activation Success'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password user
     */
    public function changePassword(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['user']['uuid']) || empty($data['user']['password'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload: uuid or password missing'
                ], 400);
            }

            $user = User::where('uuid', $data['user']['uuid'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->password = Hash::make($data['user']['password']);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
