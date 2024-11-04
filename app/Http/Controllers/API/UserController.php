<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getListOfUsers(Request $request)
    {
        $limit = $request->query('limit', 10);
        return $this->userService->getUsers($limit);
    }

    public function getUserDetails($id)
    {
        return $this->userService->getUserById($id);
    }

    public function updateUser($id, UpdateUserRequest $request)
    {
        return $this->userService->updateUser($id, $request);
    }

    public function changePassword($id, ChangePasswordRequest $request)
    {
        return $this->userService->changePassword($id, $request);
    }

    public function deleteUser($id)
    {
        return $this->userService->deleteUser($id);
    }

    public function getAllUserBooks($id)
    {
        return $this->userService->getAllUserBooks($id);
    }
}
