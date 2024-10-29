<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function getListOfUsers(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $users = $this->userModel->paginate($limit);
            $userCollection = new UserCollection($users);
            return response()->json($userCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of users failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserDetails($id)
    {
        try {
            $user = $this->userModel->getUserById($id);
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
            $user = $this->userModel->getUserById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $user->updateUser($request, $id);

            $updatedUser = $this->userModel->getUserById($id);
            $userResource = new UserResource($updatedUser);
            return response()->json(['success' => 'User updated successfully', 'user' => $userResource], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update user failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request, $id)
    {
        try {
            $user = $this->userModel->getUserById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $response = $this->userModel->changePassword($request, $id);
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Change password failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = $this->userModel->getUserById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $this->userModel->deleteUser($id);
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete user failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllUserBooks($id)
    {
        try {
            $user = $this->userModel->getUserById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $books = $user->books;
            return response()->json($books, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get all books of user failed', 'message' => $e->getMessage()], 500);
        }
    }
}
