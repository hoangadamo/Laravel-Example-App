<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function getListOfUsers()
    {
        try {
            $users = $this->userModel->get();
            $userCollection = new UserCollection($users);
            return response()->json($userCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of users failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserDetails($id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $userResource = new UserResource($user);
            return response()->json($userResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get user detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateUser(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            // Log::info('Request data:', $request->all());
            $data = [
                'name' => $request->name,
                'age' => $request->age
            ];
            if ($request->hasFile('image')) {
                $data['imageUrl'] = $this->userModel->uploadFile($request->file('image'));
            }
            $user->update(array_filter($data));
            $userResource = new UserResource($user);
            return response()->json($userResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update user failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request, $id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $data = $request->only(['oldPassword', 'newPassword', 'confirmPassword']);

            if (!Hash::check($data['oldPassword'], $user->password)) {
                return response()->json(['error' => 'Old password is incorrect'], 400);
            }
            if ($data['newPassword'] !== $data['confirmPassword']) {
                return response()->json(['error' => 'New password and confirm password do not match'], 400);
            }
            if ($data['newPassword'] === $data['oldPassword']) {
                return response()->json(['error' => 'New password must be different from the current password'], 400);
            }

            $user->update(['password' => Hash::make($data['newPassword'])]);

            return response()->json(['message' => 'Password changed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Change password failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete user failed', 'message' => $e->getMessage()], 500);
        }
    }
}
