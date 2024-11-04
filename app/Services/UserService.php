<?php

namespace App\Services;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

use function App\Helpers\uploadFile;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function storeUser($request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'password' => $request->password
        ];
        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('image')) {
            $IMAGE_URL = uploadFile($request->file('image'));
            $data['imageUrl'] = $IMAGE_URL;
        }
        $user = $this->userRepository->create($data);
        return $user;
    }

    public function getUsers($limit)
    {
        try {

            $users = $this->userRepository->paginate($limit);
            $userCollection = new UserCollection($users);
            return response()->json($userCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of users failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserById($id)
    {
        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $userResource = new UserResource($user);
            return response()->json($userResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get user detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateUser($id, $request)
    {
        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $data = [
                'name' => $request->name,
                'age' => $request->age,
                'password' => $request->password
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            if ($request->hasFile('image')) {
                $IMAGE_URL = uploadFile($request->file('image'));
                $data['imageUrl'] = $IMAGE_URL;
            }

            $this->userRepository->update($id, array_filter($data));

            $updatedUser = $this->userRepository->getById($id);
            $userResource = new UserResource($updatedUser);
            return response()->json(['success' => 'User updated successfully', 'user' => $userResource], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update user failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function changePassword($id, $request)
    {
        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $data = [
                'oldPassword' => $request->oldPassword,
                'newPassword' => $request->newPassword,
                'confirmPassword' => $request->confirmPassword
            ];

            if (!Hash::check($data['oldPassword'], $user->password)) {
                return response()->json(['error' => 'Old password is incorrect'], 400);
            }
            if ($data['newPassword'] !== $data['confirmPassword']) {
                return response()->json(['error' => 'New password and confirm password do not match'], 400);
            }
            if ($data['newPassword'] === $data['oldPassword']) {
                return response()->json(['error' => 'New password must be different from the current password'], 400);
            }

            $this->userRepository->update($id, ['password' => Hash::make($data['newPassword'])]);
            return response()->json(['message' => 'Password changed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Change password failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $this->userRepository->delete($id);
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete user failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllUserBooks($id)
    {
        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $books = $user->books;
            return response()->json($books, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get all books of user failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $this->userRepository->update($id, ['isActive' => !$user->isActive]); // does not works
            return redirect()->back()->with('success', 'User status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
